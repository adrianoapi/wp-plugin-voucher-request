<?php
class Orm
{
    private $table;
    private $wpdb;

    public function __construct($table, $wpdb)
    {
        $this->table = $wpdb->prefix.$table;
        $this->wpdb  = $wpdb;
    }

    public function select(array $data)
    {
        $select = "SELECT * FROM `{$this->table}` 
               WHERE ".$this->buildQuery($data);
        return $this->wpdb->get_results($select);
    }

    public function insert($obj)
    {
        $insert = "INSERT INTO `{$this->table}` (`id`, `type`, `price_buy`, `price_sell`, `date`)
                    VALUES (NULL, 'Dólar', '{$obj->cotacaoCompra}', '{$obj->cotacaoVenda}', '".date('Y-m-d')."')";
        return $this->wpdb->query($insert);
    }

    public function update()
    {
        //
    }

    public function delete(int $id)
    {
        return $this->wpdb->query("DELETE FROM {$this->table} WHERE id = {$id}");
    }
    
    public function drop()
    {
        return $this->wpdb->query("DROP TABLE IF EXISTS {$this->table}");
    }

    private function buildQuery(array $data)
    {
        $query = NULL;
        foreach($data as $value):
            $query .= " {$value},";
        endforeach;
        return substr($query,0,-1);
    }
}
?>