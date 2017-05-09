<?php
/*
  Plugin Name: Request Voucher
  Description: Plugin para pegar os cadastros de Voucher
  Version: 0.1
  Author: Evolutime
  Author URI: https://www.evolutime.com.br/
 */
register_activation_hook(__FILE__, 'voucher_on_activation');
register_deactivation_hook(__FILE__, 'voucher_on_deactivation');
register_uninstall_hook(__FILE__, 'voucher_on_uninstall');

function voucher_on_activation()
{
    global $wpdb;
    # Cria a tabela para armazenar id dos posts
    //$table_name = $wpdb->prefix . "voucher_data";
    //$create = "CREATE TABLE `{$table_name}` ( `id` INT NOT NULL AUTO_INCREMENT , `type` VARCHAR(20) NOT NULL , `parent_id` INT(11) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
    //$wpdb->query($create);
    # Cria o post e retorna a id
    $my_post = array(
        'post_title' => 'My post',
        'post_content' => '[voucher_search] [voucher_results]',
        'post_status' => 'publish',
        'post_password' => '123456',
        'post_author' => get_current_user_id(),
        'post_type' => 'page',
    );

    # Insira o post no banco de dados
    $post_id = wp_insert_post($my_post, '');
    # Inserte o id do post criado
    //$insert = "INSERT INTO `{$table_name}` (`id`, `type`, `parent_id`) VALUES (NULL, 'page', '{$post_id}')";
    //$wpdb->query($insert);
}

function voucher_on_deactivation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "voucher_data";
    $table_posts = $wpdb->prefix . "posts";
    # Exclui registro do post
    $wpdb->query("DELETE FROM $table_posts WHERE post_title='My post'");
    # Exlui a tabela de registro de post
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
}

function voucher_on_uninstall()
{
    #$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}voucher_data");
}

function voucher_register_table_results()
{
    global $wpdb;
    #$table_name = "{$wpdb->prefix}posts";
    $curso = !empty($_POST['curso']) ? " AND cursos.id = " . $_POST['curso'] : NULL;
    $unidade = !empty($_POST['unidade']) ? ' AND unidades.id = ' . $_POST['unidade'] : NULL;
    $divulgador = !empty($_POST['divulgador']) ? " INNER JOIN divulgadores ON (clientes.divulgador_id = divulgadores.id AND clientes.divulgador_id = {$_POST['divulgador']})" : " LEFT JOIN divulgadores ON (clientes.divulgador_id = divulgadores.id)";
    # Query
    $sql = $wpdb->get_results("SELECT clientes.*, unidades.nome AS unidade,cursos.nome AS curso, divulgadores.nome AS divulgador FROM clientes"
            . " INNER JOIN unidades ON (clientes.unidade_id = unidades.id{$unidade})"
            . " INNER JOIN cursos ON (clientes.curso_id = cursos.id{$curso})"
            . $divulgador
            . " GROUP BY clientes.id");
    ?>
    <table class="table">
        <tr>
            <td class="col-md-1">#</td>
            <td class="col-md-2">divulgador</td>
            <td class="col-md-3">nome</td>
            <td class="col-md-2">unidade</td>
            <td class="col-md-4">curso</td>
        </tr>
        <?php
        $count = 1;
        foreach ($sql as $row):
            $div = isset($row->divulgador) ? $row->divulgador : '-';
            echo "<tr>";
            echo "  <td>{$count}</td>";
            echo "  <td>{$div}</td>";
            echo "  <td>{$row->nome}</td>";
            echo "  <td>{$row->unidade}</td>";
            echo "  <td>{$row->curso}</td>";
            echo "</tr>";
            $count++;
        endforeach;
        ?>
    </table>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="url"><?= "URL para " . utf8_encode('divulgação') ?></label>
                <input type="text" value="" id="url" class="form-control">
            </div>
        </div>
    </div>

    <script>
        var divulgador = document.getElementById("divulgador");
        var divulgador_val = divulgador.options[divulgador.selectedIndex].value;
        var unidade = document.getElementById("unidade");
        var unidade_val = unidade.options[unidade.selectedIndex].value;
        var unidade_str = unidade.options[unidade.selectedIndex].text;
        var url = "";
        if (unidade_val !== "" && unidade_str !== "Todas") {
            url = "http://evolutime.com.br/cadastro/?unidade=" + unidade_str;
            if (divulgador_val !== "") {
                url += "&div=" + divulgador_val;
            }
        }
        document.getElementById('url').value = url;
    </script>
    <?php
}

function voucher_form_search()
{
    global $wpdb;
    $divulgador = $wpdb->get_results("SELECT * FROM divulgadores ORDER BY nome ASC");
    $cursos = $wpdb->get_results("SELECT * FROM cursos ORDER BY nome ASC");
    $unidades = $wpdb->get_results("SELECT * FROM unidades ORDER BY nome ASC");
    # REQUEST POST
    $r_divulgador = isset($_POST['divulgador']) ? $_POST['divulgador'] : NULL;
    $r_curso = isset($_POST['curso']) ? $_POST['curso'] : NULL;
    $r_unidade = isset($_POST['unidade']) ? $_POST['unidade'] : NULL;
    ?>
    <form action="" method="post">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="tipo">Divulgador</label>
                    <select name="divulgador" class="form-control" id="divulgador">
                        <option value="">Todos</option>
                        <?php foreach ($divulgador as $divulgador): ?>
                            <option value="<?= $divulgador->id ?>" <?= $divulgador->id == $r_divulgador ? 'selected' : NULL ?>><?= $divulgador->nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo">Unidade</label>
                    <select name="unidade" class="form-control" id="unidade">
                        <option value="">Todas</option>
                        <?php foreach ($unidades as $unidade): ?>
                            <option value="<?= $unidade->id ?>" <?= $unidade->id == $r_unidade ? 'selected' : NULL ?>><?= $unidade->alias ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo">Curso</label>
                    <select name="curso" class="form-control" id="curso">
                        <option value="">Todos</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= $curso->id ?>" <?= $curso->id == $r_curso ? 'selected' : NULL ?>><?= $curso->nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="exportar">Exportar</label>
                    <select name="exportar" class="form-control" id="exportar">
                        <option value="0" selected><?= utf8_encode('Não') ?></option>
                        <option value="1">Sim</option>
                    </select>
                </div>
            </div>
            <div class="col-md-1">
                <label for="submit">&nbsp;</label>
                <button type="submit" class="btn btn-default">filtrar</button>
            </div>
        </div>
    </form>
    <?php
}

add_shortcode('voucher_search', 'voucher_form_search');
add_shortcode('voucher_results', 'voucher_register_table_results');
?>