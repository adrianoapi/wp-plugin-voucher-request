<?php
class Structure
{
    private $tempalte;
    private $data;
    private $configuration;

    public function __construct($configuration, $template, $data)
    {
        $this->template      = $template;
        $this->data          = $data;
        $this->configuration = $configuration;
    }

    public function render()
    {
        if($this->template == 'table')
        {
            return $this->tamplateTable();
        }
        elseif($this->template == 'form')
        {
            return $this->tamplateForm();
        }
    }

    private function tamplateForm()
    {
        $html = NULL;
        $i = 0;
        foreach($this->data as $value):
            $color = $i%2 == 0 ? '#dbe5f1' : '#fff';
            $html .= '
            <tr style="background-color: '.$color.'; padding: 5px;">
            <td>'.$value->type.'</td>
            <td>R$ '.number_format($value->price_buy, 2, ',', '.').'</td>
            <td>R$ '.number_format($value->price_sell, 2, ',', '.').'</td>
            <td>'.$this->dateBr($value->date).'</td>
            <td><a href="./admin.php?page='.$this->configuration::getPluginName().'&id='.$value->id.'" target="_parent">Delete</a></td>
            </tr>';
            $i++;
        endforeach;

        $content = file_get_contents(__DIR__."/../view/form.html");
        $content = str_replace('[@TITLE]', $this->configuration::getTitle(), $content);
        $content = str_replace('[@TBODY]', $html, $content);

        return $content;
    }

    private function tamplateTable()
    {
        $html = NULL;
        foreach($this->data as $value):
            $html .= '
            <tr style="background-color: #dbe5f1; padding: 5px;">
                <td>'.$value->type.'</td>
                <td>Compra</td>
                <td>R$ '.number_format($value->price_buy, 2, ',', '.').'</td>
            </tr>
            <tr style="background-color: #fff; padding: 5px;">
            <td>'.$value->type.'</td>
            <td>Venda</td>
            <td>R$ '.number_format($value->price_sell, 2, ',', '.').'</td>
            </tr>
            <tr style="background-color: #dbe5f1; padding: 5px;">
                <td>Data</td>
                <td colspan="2">'.$this->dateBr($value->date).'</td>
            </tr>';
        endforeach;

        return str_replace('[@TBODY]', $html, file_get_contents(__DIR__."/../view/table.html"));
    }

    private function dateBr($string)
    {
        $str = explode('-',$string);
        return $str[2].'/'.$str[1].'/'.$str[0];
    }

}

?>