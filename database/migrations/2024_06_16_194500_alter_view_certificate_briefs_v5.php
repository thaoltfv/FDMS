<?php

use Illuminate\Database\Migrations\Migration;

class AlterViewCertificateBriefsV4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS view_certificate_briefs");

        DB::statement("
        create MATERIALIZED VIEW view_certificate_briefs
            AS
            select t1.id, t1.created_at, t1.updated_at, t1.status,
                    case t1.status
                        when 1 then 'Tiếp nhận hồ sơ'
                        when 10 then 'Phân hồ sơ'
                        when 2 then 'Thẩm định'
                        when 3 then 'Duyệt giá'
                        when 4 then 'Hoàn thành'
                        when 5 then 'Hủy'
                        when 7 then 'Duyệt phát hành'
                        when 8 then 'In hồ sơ'
                        when 9 then 'Bàn giao khách hàng'
                    end as status_text,
					t1.sub_status,
                    t1.status_expired_at,
                    t1.status_updated_at,
                    coalesce(t2.description) as expire_time,
                    coalesce(t1.appraiser_id,-1) as appraiser_id,
                    coalesce(t3.name,'unknown') as appraiser_name,
                    coalesce(t1.appraiser_sale_id,-1) as appraiser_sale_id,
                    coalesce(t4.name,'unknown') as appraiser_sale_name,
                    coalesce(t1.appraiser_perform_id,-1) as appraiser_perform_id,
                    coalesce(t5.name,'unknown') as appraiser_perform_name,
                    coalesce(t1.branch_id,coalesce(t6.branch_id,-1)) as branch_id,
                    coalesce(t7.name,coalesce(t8.name, 'unknown')) as branch_name,
                    coalesce(t1.service_fee, 0) as service_fee
                from certificates t1
                    left outer join (select tt1.id , tt2.description
                                    from certificates tt1 inner join certificate_dictionaries tt2 on tt2.type ='PROCESSING_TIME'
                                    and (case tt1.status
                                            when 1 then 'MOI'
                                            when 2 then 'DANG-THAM-DINH'
                                            when 3 then 'DANG-DUYET'
                                            end ) = tt2.acronym and tt2.status =1
                                    and tt1.deleted_at is null
                                    ) t2 on t1.id = t2.id
                    left outer join appraisers t3 on t1.appraiser_id = t3.id and t3.deleted_at is null
                    left outer join appraisers t4 on t1.appraiser_sale_id = t4.id and t4.deleted_at is null
                    left outer join appraisers t5 on t1.appraiser_perform_id = t5.id and t5.deleted_at is null
                    left outer join appraisers t6 on t1.created_by = t6.user_id and t6.deleted_at is null
                    left outer join branches t7 on t1.branch_id = t7.id and t7.deleted_at is null
                    left outer join branches t8 on t6.branch_id = t8.id and t8.deleted_at is null
                where t1.deleted_at is null 
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
