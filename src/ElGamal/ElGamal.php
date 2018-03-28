<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-15
 * Time: 17:16
 */

namespace ElGamal;

use Components\Field\FpField;
use ElGamal\Components\Receiver;
use ElGamal\Components\Sender;
use EllipticCurveAlgorithms\EllipticCurve;

class ElGamal
{
    /**
     * @var FpField
     */
    protected $field;

    /**
     * @var EllipticCurve
     */
    protected $e;

    /**
     * @var \EllipticCurveAlgorithms\Point
     */
    protected $G;

    /**
     * @var integer
     */
    protected $rzG;

    public function __construct($params)
    {
        $this->field = new FpField($params['p']);
        $this->e = new EllipticCurve($this->field, $params['a'], $params['b']);

        $this->G = $this->e->createPoint($params['G']['x'], $params['G']['y']);
        $this->rzG = $params['#G'] ?? 0;
    }

    public function run(string $mode, string $messageFile, string $outputFile)
    {
        if ($mode == 'S') {
            $obj = new Sender($this->field, $this->e, $this->G, $this->rzG, $messageFile);
        } else if ($mode == 'R') {
            $obj = new Receiver($this->field, $this->e, $this->G, $this->rzG, $outputFile);
        } else {
            die('Nieznany tryb pracy');
        }

        $obj->run();
    }
}