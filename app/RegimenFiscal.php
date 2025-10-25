<?php

namespace App;

enum RegimenFiscal: string
{
    case General = '601';
    case PersonasMoralesConFinesNoLucrativos = '603';
    case SueldosYSalarios = '605';
    case Arrendamiento = '606';
    case Demas = '607';
    case HonorariosAsimiladosSalarios = '608';
    case ResidentesExtranjero = '610';
    case IngresosDividendos = '611';
    case PersonasFisicasActividadesEmpresariales = '612';
    case RegimenesPreferentes = '614';
    case RegimenesOpcionales = '615';
    case SinObligacionesFiscales = '616';
    case SociedadesCooperativasProduccion = '620';
    case IncorporacionFiscal = '621';
    case ActividadesAgricolasGanaderasSilvicolas = '622';
    case GruposSociedades = '623';
    case Coordinados = '624';
    case RegimenesECFAW = '625';
    case SimplificadoConfianza = '626';

    public function label(): string
    {
        return match ($this) {
            self::General => '601 - General de Ley Personas Morales',
            self::PersonasMoralesConFinesNoLucrativos => '603 - Personas Morales con Fines no Lucrativos',
            self::SueldosYSalarios => '605 - Sueldos y Salarios e Ingresos Asimilados a Salarios',
            self::Arrendamiento => '606 - Arrendamiento',
            self::Demas => '607 - Régimen de Enajenación o Adquisición de Bienes',
            self::HonorariosAsimiladosSalarios => '608 - Demás ingresos',
            self::ResidentesExtranjero => '610 - Residentes en el Extranjero sin Establecimiento Permanente en México',
            self::IngresosDividendos => '611 - Ingresos por Dividendos (socios y accionistas)',
            self::PersonasFisicasActividadesEmpresariales => '612 - Personas Físicas con Actividades Empresariales y Profesionales',
            self::RegimenesPreferentes => '614 - Ingresos por intereses',
            self::RegimenesOpcionales => '615 - Régimen de los ingresos por obtención de premios',
            self::SinObligacionesFiscales => '616 - Sin obligaciones fiscales',
            self::SociedadesCooperativasProduccion => '620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
            self::IncorporacionFiscal => '621 - Incorporación Fiscal',
            self::ActividadesAgricolasGanaderasSilvicolas => '622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            self::GruposSociedades => '623 - Opcional para Grupos de Sociedades',
            self::Coordinados => '624 - Coordinados',
            self::RegimenesECFAW => '625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
            self::SimplificadoConfianza => '626 - Régimen Simplificado de Confianza',
        };
    }
}
