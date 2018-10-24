<?php namespace Octobro\Gamify\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator as QrCode;

/**
 * Vouchers Back-end Controller
 */
class Vouchers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octobro.Gamify', 'gamify', 'vouchers');
    }

    public function downloadQR($code)
    {
        $qrCode = new QrCode();
        $decoded = $qrCode->format('png')->merge('\logo.png', .1)->encoding('UTF-8')->size(1000)->generate($code);
        $file = "$code-QRcode.png";
        file_put_contents($file, $decoded);
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            unlink($file);
            exit;
        }
    }
}
