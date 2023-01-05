<?php

namespace App\Collections;

use Exception;

use App\Models\EmpresaEsquema;
use App\Models\VendedorEsquema;
use Illuminate\Database\Eloquent\Collection;

class PrestamoCollection extends Collection
{
    public function __construct($prestamos, $esEmpresa)
    {
        $this->prestamos = $prestamos;
        $this->esEmpresa = $esEmpresa;
    }

    public function agruparPrestamos()
    {
        $prestamos = $this->prestamos;
        $esEmpresa = $this->esEmpresa;
        try {
            if ($esEmpresa) {
                $prestamos = $prestamos->groupBy('id_empresa_linea');
            } else {
                $prestamosAgrupados = collect([]);
                foreach ($prestamos->groupBy(['id_empresa_linea', 'id_vendedor']) as $prestamosPorEmpresa) {
                    foreach ($prestamosPorEmpresa as $prestamosPorVendedor) {
                        $prestamosAgrupados->push($prestamosPorVendedor);
                    }
                }
                $prestamos = $prestamosAgrupados;
            }
            $this->prestamos = $prestamos;
        } catch (Exception $e) {
            throw $e;
        }
    }

    //Agrupar prestamos antes de usar esta funcion.
    public function getEsquemas()
    {
        $prestamos = $this->prestamos;
        $esquemas = null;
        $esEmpresa = $this->esEmpresa;
        try {
            if ($esEmpresa) {
                $indexConsulta = [];
                foreach ($prestamos as $idLinea => $value) {
                    $indexConsulta[] = $idLinea;
                }

                $lineasStr = implode($indexConsulta);
                $querySql = "SELECT * FROM empresas_esquemas ee WHERE
                         EXISTS (
                            SELECT 1
                            FROM
                                empresas_lineas el
                                INNER JOIN empresas_esquemas_lineas eel ON
                                    el.id = eel.id_empresa_linea
                            WHERE
                                ee.id = eel.id_empresa_esquema
                                AND el.id IN ({$lineasStr})
                            LIMIT 0,1
                    );";

                $esquemas = EmpresaEsquema::query($querySql)->with(['lineas','empresa'])->lockForUpdate()->get();
            } else {
                $indexConsulta = [];

                foreach ($prestamos as $idVendedor => $grupo) {
                    $indexConsulta[$idVendedor] = [];
                    foreach ($grupo as $idLinea => $value) {
                        $indexConsulta[$idVendedor][] = $idLinea;
                    }
                }

                $headerSql = "SELECT * FROM vendedores_esquemas ve WHERE (";

                $iterableSql = [];
                $querySql = "";

                foreach ($indexConsulta as $idVendedor => $lineas) {
                    $lineasStr = implode(",", $lineas);
                    $iterableSql[] = "(
                            ve.id_vendedor = {$idVendedor}
                            AND EXISTS (
                                SELECT 1
                                FROM
                                    empresas_lineas el
                                    INNER JOIN vendedores_esquemas_lineas vel ON
                                        el.id = vel.id_empresa_linea
                                WHERE
                                    ve.id = vel.id_vendedores_esquemas
                                    AND el.id IN ({$lineasStr})
                                LIMIT 0,1
                            )
                        )";
                }

                $querySql =  $headerSql . implode(" OR ", $iterableSql) . ")";

                $esquemas = VendedorEsquema::query($querySql)->with(['lineas','vendedor'])->lockForUpdate()->get();
            }
            return $esquemas;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
