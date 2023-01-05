<?php

namespace App\Collections;

use App\Exceptions\ApiException;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class EsquemaCollection extends Collection
{
    public function __construct($esquemas, $esEmpresa)
    {
        $this->esquemas = $esquemas;
        $this->esEmpresa = $esEmpresa;
    }

    public function getEsquema($idLinea, $idVendedor, $linea, $vendedor)
    {
        $esquema = null;
        $esEmpresa = $this->esEmpresa;
        $esquemas = $this->esquemas;

        $count = 0;

        $empresa = null;
        $mensajeError = "";

        if ($esEmpresa && !empty($linea)) {
            $empresa = $linea->empresa;
        }

        try {
            if ($esEmpresa) {
                foreach ($esquemas as $e) {
                    foreach ($e->lineas as $l) {
                        if ($l->id == $idLinea) {
                            $esquema = $e;
                            $count++;
                        }
                    }
                }
            } else {
                foreach ($esquemas as $e) {
                    if ($e->id_vendedor == $idVendedor) {
                        foreach ($e->lineas as $l) {
                            if ($l->id == $idLinea) {
                                $esquema = $e;
                                $count++;
                            }
                        }
                    }
                }
            }

            if ($count === 0 || $count > 1) {

                $mensajeError = " a la linea '{$linea->nombre}'";

                if ($esEmpresa) {
                    $razonSocial = null;
                    if (!empty($empresa)) {
                        $razonSocial = $empresa->razon_social;
                    }
                    $mensajeError .= "de la empresa '{$razonSocial}'.";
                } else {
                    $razonSocial = null;
                    $cuit = null;
                    if (!empty($vendedor)) {
                        $razonSocial = $vendedor->razon_social;
                        $cuit = $vendedor->cuit;
                    }
                    $mensajeError .= ", para el vendedor '{$razonSocial}'";
                    $mensajeError .= " cuit {$cuit}.";
                }

                if ($count === 0) {
                    $mensajeError = "No se encuentra un esquema asociado" . $mensajeError;
                } else {
                    $mensajeError = "Hay mas de un esquema asociado" . $mensajeError;
                }

                throw new ApiException($mensajeError);
            }


            return $esquema;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
