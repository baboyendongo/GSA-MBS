<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstancelsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['2026-10-15','82754','RSD','000369','35301','FFRIM',null,'Traitement','CI',null,null,null],
            ['2026-10-15','82815','RSD','010191','13120327','Presta',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-10-15','82816','RSD','010229','13281348','Tiriss',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-10-14','77687','TRL','000159','33638','MTNIMA',null,'Intégration','IP-MPLS',null,'2026-10-18',null],
            ['2026-10-08','49126','CLS','010455','88299','Douane',null,'Intégration','IP-MPLS',null,null,null],
            ['2026-10-01','12104','CLS',null,'15829146','CNSU',null,'Etude','Sce Transmission',null,null,null],
            ['2026-10-01','12524','MLS','010433','15397723','AFR-IX',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-10-01','12526','MLS','010434','15397723','AFR-IX',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-10-01','12528','MLS','010435','15397723','AFR-IX',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-10-01','12810','CLS',null,'12705','BPM','ALG','Etude','Sce Transmission',null,null,'Zone de gestion à corriger par MBS'],
            ['2026-10-01','12823','CLS',null,'12705','BPM','Elghayra','Etude','Sce Transmission',null,null,'Zone de gestion à corriger par MBS'],
            ['2026-10-01','12824','CLS',null,'12705','BPM','BSK','Etude','Sce Transmission',null,null,'Zone de gestion à corriger par MBS'],
            ['2026-10-01','12825','CLS',null,'12705','BPM','AKJ','Etude','Sce Transmission',null,null,'Zone de gestion à corriger par MBS'],
            ['2026-09-29','231165','RTO','010129','1379','IPN',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-09-29','231264','RTO','010129','1379','IPN',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-09-28','211924','TRL','010030','1484','CNSS',null,'Intégration','IP-MPLS',null,null,null],
            ['2026-09-28','217898','SPO','010060','10969739','ISMM',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-09-28','217902','SPO','010195','1474','Chambre C&I',null,'Traitement','IP-MPLS',null,null,null],
            ['2026-09-25','201544','TRL','010196','1335','SOMELEC',null,'Intégration','IP-MPLS',null,null,null],
            ['2026-09-23','192562','RCL',null,'15397723','AFR-IX',null,'Traitement','Sce Réclamations',null,null,null],
            ['2026-09-15','160037','CLS','010456','1621','INRSP','NKC','Intégration','IP-MPLS',null,null,null],
            ['2026-09-14','155817','DRG','010222','10993429','SCOMAT',null,'Traitement','MBS',null,null,'Attente autorisation d’accès'],
            ['2026-09-08','124374','MLS','010029','57121','Huawei',null,null,null,null,null,null],
            ['2026-08-31','296786','RSD','010298','5571245','AMB U.S.A','NKC',null,null,null,null,null],
            ['2026-08-19','219589','CLS',null,'55644','SG','AKJ',null,null,null,null,null],
            ['2026-08-14','194549','CLS',null,'13116961','SEHDINI','NKC',null,null,null,null,null],
            ['2026-08-03','21354','DRG','001061','1689','SMCP',null,null,null,null,null,null],
            ['2026-08-03','24813','CLS','010454','15501412','MAURIGALLI','MDR',null,null,null,null,null],
            ['2026-08-03','27952','CST','010082','6616833','Banque Mondiale','NKC',null,null,null,null,null],
            ['2026-08-03','27955','CST','010420','15397723','AFR-IX','NKC',null,null,null,null,null],
            ['2026-08-03','27956','CST','010469','91699','MINIS Education','NKC',null,null,null,null,null],
            ['2026-08-03','27959','CST','010178','9168017','Projet INAYA','NKC',null,null,null,null,null],
            ['2026-07-30','682397','RSD','001107','4703310','MINAA MAURITANIA','NKC',null,null,null,null,null],
            ['2026-06-23','219015','CLS','010449','3982480','IMT','NKC',null,null,null,null,null],
            ['2026-05-26','542065','CLS','010448','14808565','Mauricim','NKC',null,null,null,null,null],
            ['2026-05-26','542625','CLS','010447','14808565','Mauricim','NKC',null,null,null,null,null],
            ['2026-04-23','689703','CLS','010440','1491','BAMIS','NKC','Intégration','IP-MPLS',null,'2026-10-20',null],
            ['2026-04-13','643844','CLS','010436','3982480','IMT','NKC',null,null,null,null,null],
            ['2026-03-06','42330','CLS','010437','1938247','ANRPTS','NKC',null,null,null,null,null],
            ['2026-01-19','143429','CLS',null,'55644','SG','NKC',null,null,null,null,null],
            ['2026-01-19','143466','CLS',null,'55644','SG','NKC',null,null,null,null,null],
            ['2025-11-05','44458','RCL',null,'9810204','Mauritanie Telecom',null,null,null,null,null,null],
            ['2025-09-24','407705','RCL','010305','4207033','SinoHydro',null,null,null,null,null,null],
            ['2025-06-23','93505','CLS','010341','15397723','AFR-IX','NKC',null,null,null,null,null],
            ['2025-04-17','61542','CLS','010334','53943','MAURI GAZ','NKC',null,null,null,null,null],
            ['2025-04-15','52973','CLS','010328','12705','BPM','BTL',null,null,null,null,null],
            ['2024-10-21','261033','RCL',null,'13171921','PolyChangda',null,null,null,null,null,null],
            ['2024-08-27','557612','RCL',null,'6510119','Cimenterie Sahel',null,null,null,null,null,null],
            ['2024-08-13','68176','RCL',null,'57121','Huawei',null,null,null,null,null,null],
            ['2024-05-16','70771','RCL',null,'4394123','Les impots',null,null,null,null,null,null],
            ['2024-05-02','9267','RCL',null,'13802307','Port Ndagou',null,null,null,null,null,null],
        ];

        $columns = [
            'date_evenement',
            'evenement',
            'operation',
            'ligne',
            'code_client',
            'client',
            'ville',
            'phase',
            'responsable',
            'delais',
            'date_cloture',
            'observations',
        ];

        foreach ($data as $row) {
            $record = array_combine($columns, $row);

            $payload = [
                'date' => $record['date_evenement'],
                'evenement' => $record['evenement'],
                'operation' => $record['operation'],
                'ligne' => $record['ligne'],
                'codeclient' => $record['code_client'],
                'clients' => $record['client'],
                'ville' => $record['ville'],
                'phase' => $record['phase'],
                'responsable' => $record['responsable'],
                'delai' => $record['delais'],
                'date_cloture' => $record['date_cloture'],
                'observation' => $record['observations'],
            ];

            DB::table('instance_l_s')->updateOrInsert(
                ['evenement' => $payload['evenement']],
                $payload
            );
        }

    }
}
