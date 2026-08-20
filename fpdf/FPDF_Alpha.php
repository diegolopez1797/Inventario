<?php

require(__DIR__ . '/../fpdf/fpdf.php');

class FPDF_Alpha extends FPDF
{
    protected $extgstates = [];

    /**
     * Define el nivel de opacidad (0.0 = invisible, 1.0 = opxaaco)
     * $mode puede ser 'Normal', 'Multiply', 'Screen', 'Darken', 'Lighten', etc.
     */
    function SetAlpha($alpha, $mode = 'Normal')
    {
        // Busca si ya existe un extgstate con estos parámetros
        $key = md5($alpha . $mode);

        if (!isset($this->extgstates[$key])) {
            $n = count($this->extgstates) + 1;
            $this->extgstates[$key] = [
                'n'    => $n,
                'ca'   => $alpha, // opacidad para trazos (stroke)
                'CA'   => $alpha, // opacidad para relleno (fill) / imágenes
                'mode' => $mode,
            ];
        }

        $gs = $this->extgstates[$key];
        $this->_out(sprintf('/GS%d gs', $gs['n']));
    }

    function _enddoc()
    {
        if (!empty($this->extgstates)) {
            $this->_putextgstates();
        }
        parent::_enddoc();
    }

    protected function _putextgstates()
    {
        foreach ($this->extgstates as $k => $gs) {
            $this->_newobj();
            $this->extgstates[$k]['obj_id'] = $this->n;
            $this->_out('<<');
            $this->_out('/Type /ExtGState');
            $this->_out(sprintf('/ca %.2F', $gs['ca']));
            $this->_out(sprintf('/CA %.2F', $gs['CA']));
            $this->_out(sprintf('/BM /%s', $gs['mode']));
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        if (!empty($this->extgstates)) {
            $this->_out('/ExtGState <<');
            foreach ($this->extgstates as $gs) {
                $this->_out(sprintf('/GS%d %d 0 R', $gs['n'], $gs['obj_id']));
            }
            $this->_out('>>');
        }
    }

    function _putresources()
    {
        if (!empty($this->extgstates)) {
            $this->_putextgstates();
        }
        parent::_putresources();
    }
}