<?php

namespace App;

enum UsoCFDI: string
{
    case Adquisicion = 'G01';
    case Devoluciones = 'G02';
    case GastosGeneral = 'G03';
    case Construcciones = 'I01';
    case MobilityEquipo = 'I02';
    case EquipoComputo = 'I03';
    case EquipoOficina = 'I04';
    case EquipoTransporte = 'I05';
    case EquipoComunicacion = 'I06';
    case EquipoDeporte = 'I07';
    case OtrosActivos = 'I08';
    case Honorarios = 'D01';
    case GastosMedicos = 'D02';
    case GastosHospitalarios = 'D03';
    case Donativos = 'D04';
    case InteresesHipotecarios = 'D05';
    case AportacionesVoluntarias = 'D06';
    case PrimasSegurosGastosMedicos = 'D07';
    case GastosTransporteEscolar = 'D08';
    case DepositosRetiro = 'D09';
    case PagoServicios = 'D10';
    case PorDefinir = 'P01';
    case SinEfectosFiscales = 'S01';
    case PagosNominaYAsimilados = 'CN01';

    public function label(): string
    {
        return match ($this) {
            self::Adquisicion => 'G01 - Adquisición de mercancías',
            self::Devoluciones => 'G02 - Devoluciones, descuentos o bonificaciones',
            self::GastosGeneral => 'G03 - Gastos en general',
            self::Construcciones => 'I01 - Construcciones',
            self::MobilityEquipo => 'I02 - Mobilidad y equipo de oficina por inversiones',
            self::EquipoComputo => 'I03 - Equipo de transporte',
            self::EquipoOficina => 'I04 - Equipo de cómputo y accesorios',
            self::EquipoTransporte => 'I05 - Dados, troqueles, moldes, matrices y herramental',
            self::EquipoComunicacion => 'I06 - Comunicaciones telefónicas',
            self::EquipoDeporte => 'I07 - Comunicaciones satelitales',
            self::OtrosActivos => 'I08 - Otra maquinaria y equipo',
            self::Honorarios => 'D01 - Honorarios médicos, dentales y gastos hospitalarios',
            self::GastosMedicos => 'D02 - Gastos médicos por incapacidad o discapacidad',
            self::GastosHospitalarios => 'D03 - Gastos funerales',
            self::Donativos => 'D04 - Donativos',
            self::InteresesHipotecarios => 'D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)',
            self::AportacionesVoluntarias => 'D06 - Aportaciones voluntarias al SAR',
            self::PrimasSegurosGastosMedicos => 'D07 - Primas por seguros de gastos médicos',
            self::GastosTransporteEscolar => 'D08 - Gastos de transportación escolar obligatoria',
            self::DepositosRetiro => 'D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones',
            self::PagoServicios => 'D10 - Pagos por servicios educativos (colegiaturas)',
            self::PorDefinir => 'P01 - Por definir',
            self::SinEfectosFiscales => 'S01 - Sin efectos fiscales',
            self::PagosNominaYAsimilados => 'CN01 - Nómina',
        };
    }
}
