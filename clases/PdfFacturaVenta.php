<?php


class PdfFacturaVenta extends FPDF
{
    
    private $nomEmpresa;
    
    public $textoFooter = "Factura de venta";
    

//Page header
    function Header()
    {
        global $textos, $sql, $sesion_usuarioSesion;
        
        $empresa = new Empresa(1);
        
        $logoEmpresa = $empresa->imagenPrincipal;
         
        $this->Image($logoEmpresa, 170, 5, 35, 10, 'jpg');

        $this->Ln(4);

        //Aqui se cargan los datos informativos de la empresa
        $this->nomEmpresa   =  $empresa->nombre;
        $nombreEmpresa      = $empresa->nombre;
        $nitEmpresa         = $empresa->nit;
        $telefono           = $empresa->telefono;
        $direccion          = $empresa->direccionPrincipal;
        $email              = $empresa->email;
        $pagina             = $empresa->paginaWeb;
        $nota               = $sql->obtenerValor('configuraciones', 'nota_factura', 'id = "1"');
        
        $notaResolucion = "";
        
        if ($empresa->regimen != "1"){
            $idResolucion   = $sql->obtenerValor('resoluciones', 'id', 'id_sede = "'.$sesion_usuarioSesion->sede->id.'" AND activo = "1"');
            $resolucion     = new Resolucion($idResolucion);

            $notaResolucion = str_replace('%1', $resolucion->numero, $textos->id('NOTA_RESOLUCION_DIAN_EN_FACTURA'));
            $notaResolucion = str_replace('%2', $resolucion->fechaResolucion, $notaResolucion);
            $notaResolucion = str_replace('%3', $resolucion->numeroFacturaInicial, $notaResolucion);
            $notaResolucion = str_replace('%4', $resolucion->numeroFacturaFinal, $notaResolucion);        

        }

        $this->SetFont('times', 'B', 7);
        $this->Cell(40, 7, $nombreEmpresa, 0, 0, 'L');
        $this->Ln(3);

        $this->SetFont('times', 'B', 7);
        $this->Cell(8, 7, $textos->id("NIT") . ': ', 0, 0, 'L');
        $this->SetFont('times', '', 7);
        $this->Cell(27, 7, $nitEmpresa, 0, 0, 'L');

        $this->SetFont('times', 'B', 7);
        $this->Cell(19, 7, '', 0, 0, 'L');
        $this->Cell(15, 7, $textos->id("DIRECCION") . ': ', 0, 0, 'L');
        $this->SetFont('times', '', 7);
        $this->Cell(20, 7, $direccion, 0, 0, 'L');

        $this->Ln(3);

        $this->SetFont('times', 'B', 7);
        $this->Cell(12, 7, $textos->id("TELEFONO") . ': ', 0, 0, 'L');
        $this->SetFont('times', '', 7);
        $this->Cell(30, 7, $telefono, 0, 0, 'L');


        $this->SetFont('times', 'B', 7);
        $this->Cell(12, 7, '', 0, 0, 'L');
        $this->SetFont('times', 'B', 7);
        $this->Cell(15, 7, $textos->id("EMAIL") . ': ', 0, 0, 'L');
        $this->SetFont('times', '', 7);
        $this->Cell(27, 7, $email, 0, 0, 'L');

        $this->SetFont('times', 'B', 7);
        $this->Cell(10, 7, $textos->id("PAGINA") . ': ', 0, 0, 'L');
        $this->SetFont('times', '', 7);
        $this->Cell(20, 7, $pagina, 0, 0, 'L');

        $this->Ln(3);

        $this->SetFont('times', 'B', 7);
        $this->Cell(22, 7, $textos->id("NOTA") . ': ', 0, 0, 'L');
        $this->SetFont('times', 'I', 7);
        $this->Ln(5);
        $this->MultiCell(200, 3, $nota, '', 'L', 0);
        
        $this->Ln(2);
        $this->SetFont('times', '', 6);
        $this->MultiCell(200, 3, $notaResolucion, '', 'L', 0); //multicel utilizado para haga el salto de linea 
        //Linea divisora de los datos de la empresa y los datos de la factura
        $this->Cell(197, 8, '', 'B', 0, 'L');        

        $this->Line(10, 24, 205, 24);

        
    }

//Page footer
    function Footer()
    {
        //Position at 1.5 cm from bottom
        $this->SetY(-10);
        
        $this->Line(10, 270, 205, 270);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Page number
        $this->Cell(0, 10, "Pagina " . $this->PageNo() . "  - ".$this->textoFooter." - fecha ".date("Y-m-d H:i:s")." - ".$this->nomEmpresa, 0, 0, 'C');
        
    }

}
