<?php
class Soap
{
    private $dateBegin;
    private $dateEnd;

    public function __construct()
    {

    }

    public function build()
    {
        $context  = stream_context_create($this->opts());
        $url      = 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaPeriodo(moeda=@moeda,dataInicial=@dataInicial,dataFinalCotacao=@dataFinalCotacao)?@moeda=%27USD%27&@dataInicial=%27'.$this->dateBegin.'%27&@dataFinalCotacao=%27'.$this->dateEnd.'%27&$top=100&$format=json';
        return file_get_contents($url, false, $this->streamSSL());
    }

    private function streamSSL()
    {
        return stream_context_create(array(
            "ssl"=>array(
                "cafile" => __DIR__."/cacert.pem",
                "verify_peer"=> true,
                "verify_peer_name"=> true
            )
        ));
    }

    private function opts()
    {
        return array('http' =>
            array(
                'method'  => 'POST',
                'header'  => "Content-Type: text/xml\r\n",
                #"Authorization: Basic ".base64_encode("$https_user:$https_password")."\r\n",
                #'content' => $body,
                'timeout' => 60
            )
        );
    }

    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function setDateBegin($value)
    {
        $this->dateBegin = $value;
        return $this;
    }

    public function setDateEnd($value)
    {
        $this->dateEnd = $value;
        return $this;
    }

}

?>