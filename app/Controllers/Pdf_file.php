<?php namespace App\Controllers;
use App\Controllers\BaseController;

class Pdf_file extends BaseController
{
  public function index(){
    $pathFile = 'pdf/factura_3901860.pdf';
    $pdfVersion = $this->pdfVersion($pathFile);
    $data['path'] = $pathFile;
    $data['pdfVersion'] = $pdfVersion;

    $view = view('imagePdf');
    $errMsj = view('errors/errorVersionPdfView', $data);

    header("Content-type:application/pdf");

    $mpdf = new \Mpdf\Mpdf();

    if($pdfVersion != '1.4')
    {
      $pathParts = pathinfo($pathFile);
      // $fileName = $pathParts['filename'] . $pathParts['extension'];
      $fileName = 'factura_3901860.pdf';
      rename($pathFile, 'pdf/old_file.pdf');
      exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile=public/pdf/$fileName public/pdf/old_file.pdf");
      if(file_exists('pdf/' . $fileName)){
        // unlink('pdf/old_file.pdf');
      }
    }
    
    $pagecount = $mpdf->SetSourceFile('pdf/' . $fileName);
    $tplId = $mpdf->ImportPage($pagecount);
    $mpdf->UseTemplate($tplId);
    $mpdf->AddPage();
    $mpdf->WriteHTML($view);
    $mpdf->Output();

    exit;
  }
 
  //Return the PDF version
  function pdfVersion($filename)
  { 
    $fp = @fopen($filename, 'rb');

    if (!$fp) {
        return 0;
    }

    /* Reset file pointer to the start */
    fseek($fp, 0);

    /* Read 20 bytes from the start of the PDF */
    preg_match('/\d\.\d/',fread($fp,20),$match);

    fclose($fp);

    if (isset($match[0])) {
        return $match[0];
    } else {
        return 0;
    }
  } 
}

//'pdf/prueba.pdf'