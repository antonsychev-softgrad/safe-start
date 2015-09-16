<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Response;
use Zend\Http\Request;

class ExportToCsvPlugin extends AbstractPlugin
{
    protected $options = array(
        'delimiter' => ';',
        'enclosure' => '"',
        'name' => 'export',
    );

    public function __invoke(array $options = array()) {
        $this->setOptions($options);
        return $this;
    }

    protected function setOptions(array $options = array()) {

//        $os = $this->getOS();
//        if(preg_match("/(windows)|(Unknown)/isU", $os, $match)) {
//            $this->options["delimiter"] = ';';
//        } else {
//            $this->options["delimiter"] = ',';
//        }

        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');
        $exportConfig = !empty($moduleConfig['export']) ? $moduleConfig['export'] : array();
        $this->options = array_merge($this->options, $exportConfig);
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        if(empty($this->options['name'])) {
            $this->options['name'] = 'export';
        }

        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getOS($userAgent = '') {
        if(empty($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        $oses = array (
            // Mircrosoft Windows Operating Systems
            'Windows 3.11' => '(Win16)',
            'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows 98' => '(Windows 98)|(Win98)',
            'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
            'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
            'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
            'Windows Server 2003' => '(Windows NT 5.2)',
            'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
            'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
            'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
            'Windows CE' => '(Windows CE)',
            // UNIX Like Operating Systems
            'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
            'Mac OS X Cheetah' => '(Mac OS X 10.0)',
            'Mac OS X Puma' => '(Mac OS X 10.1)',
            'Mac OS X Jaguar' => '(Mac OS X 10.2)',
            'Mac OS X Panther' => '(Mac OS X 10.3)',
            'Mac OS X Tiger' => '(Mac OS X 10.4)',
            'Mac OS X Leopard' => '(Mac OS X 10.5)',
            'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
            'Mac OS X Lion' => '(Mac OS X 10.7)',
            'Mac OS X' => '(Mac OS X)',
            'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
            'Open BSD' => '(OpenBSD)',
            'SunOS' => '(SunOS)',
            'Solaris 11' => '(Solaris/11)|(Solaris11)',
            'Solaris 10' => '((Solaris/10)|(Solaris10))',
            'Solaris 9' => '((Solaris/9)|(Solaris9))',
            'CentOS' => '(CentOS)',
            'QNX' => '(QNX)',
            // Kernels
            'UNIX' => '(UNIX)',
            // Linux Operating Systems
            'Ubuntu 12.10' => '(Ubuntu/12.10)|(Ubuntu 12.10)',
            'Ubuntu 12.04 LTS' => '(Ubuntu/12.04)|(Ubuntu 12.04)',
            'Ubuntu 11.10' => '(Ubuntu/11.10)|(Ubuntu 11.10)',
            'Ubuntu 11.04' => '(Ubuntu/11.04)|(Ubuntu 11.04)',
            'Ubuntu 10.10' => '(Ubuntu/10.10)|(Ubuntu 10.10)',
            'Ubuntu 10.04 LTS' => '(Ubuntu/10.04)|(Ubuntu 10.04)',
            'Ubuntu 9.10' => '(Ubuntu/9.10)|(Ubuntu 9.10)',
            'Ubuntu 9.04' => '(Ubuntu/9.04)|(Ubuntu 9.04)',
            'Ubuntu 8.10' => '(Ubuntu/8.10)|(Ubuntu 8.10)',
            'Ubuntu 8.04 LTS' => '(Ubuntu/8.04)|(Ubuntu 8.04)',
            'Ubuntu 6.06 LTS' => '(Ubuntu/6.06)|(Ubuntu 6.06)',
            'Red Hat Linux' => '(Red Hat)',
            'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
            'Fedora 17' => '(Fedora/17)|(Fedora 17)',
            'Fedora 16' => '(Fedora/16)|(Fedora 16)',
            'Fedora 15' => '(Fedora/15)|(Fedora 15)',
            'Fedora 14' => '(Fedora/14)|(Fedora 14)',
            'Chromium OS' => '(ChromiumOS)',
            'Google Chrome OS' => '(ChromeOS)',
            // Kernel
            'Linux' => '(Linux)|(X11)',
            // BSD Operating Systems
            'OpenBSD' => '(OpenBSD)',
            'FreeBSD' => '(FreeBSD)',
            'NetBSD' => '(NetBSD)',
            // Mobile Devices
            'Andriod' => '(Android)',
            'iPod' => '(iPod)',
            'iPhone' => '(iPhone)',
            'iPad' => '(iPad)',
            //DEC Operating Systems
            'OS/8' => '(OS/8)|(OS8)',
            'Older DEC OS' => '(DEC)|(RSTS)|(RSTS/E)',
            'WPS-8' => '(WPS-8)|(WPS8)',
            // BeOS Like Operating Systems
            'BeOS' => '(BeOS)|(BeOS r5)',
            'BeIA' => '(BeIA)',
            // OS/2 Operating Systems
            'OS/2 2.0' => '(OS/220)|(OS/2 2.0)',
            'OS/2' => '(OS/2)|(OS2)',
            // Search engines
            'Search engine or robot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves/Teoma)|(ia_archiver)'
        );

        foreach($oses as $os=>$pattern){
            if(preg_match("/$pattern/i", $userAgent)) {
                return $os;
            }
        }
        return 'Unknown';
    }


    public function export(array $data = array()) {
        ini_set('max_execution_time', 0);

        $csv_name = preg_replace("/\.csv$/isU", '', $this->options['name']) . '_' . gmdate('YmdHis');

        $fp = fopen('php://output', 'w');
        ob_start();
        if(!empty($data) && is_array($data)) {
            foreach ($data as $fields) {
                try {
                    fputcsv($fp, $fields, $this->options['delimiter'], $this->options['enclosure']);
                } catch (\Exception $e) {
                    ob_end_clean();
                    throw $e;
                }
            }
        }
        fclose($fp);

        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setVersion(Request::VERSION_11);
        $response->getHeaders()->addHeaders(array(
            'Content-Disposition'       => "attachment; filename={$csv_name}.csv",
            'Content-Description'       => 'File Transfer',
            'Content-type'              => 'application/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control'             => 'no-cache, must-revalidate',
            'Pragma'                    => 'public',
            'Expires'                   => 'Sat, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified'             => gmdate('D, d M Y H:i:s') . ' GMT',
        ));
        $response->setContent(ob_get_clean());

        return $response;
    }


}