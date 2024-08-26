<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class updateDataTypeCharacterVarying extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Xóa view có data lấy từ các cột cần đổi kiểu dữ liệu trước khi đổi để tránh bị lỗi
        DB::statement("DROP VIEW IF EXISTS view_selected_certificate_assets");
        DB::statement("DROP VIEW IF EXISTS view_selected_certificate_apartments");
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS view_certificate_briefs");


        // Lấy tất cả các cột có kiểu dữ liệu character varying(255)
        $columns = DB::select("
            SELECT table_name, column_name 
            FROM information_schema.columns 
            WHERE data_type = 'character varying' 
            AND character_maximum_length = 255
        ");

        // Tạo các câu lệnh ALTER TABLE để chuyển đổi các cột này thành kiểu text
        foreach ($columns as $column) {
            $table = $column->table_name;
            $col = $column->column_name;
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$table}.{$col} TYPE text");
        }

        // Tiến hành tạo lại các view 
        DB::statement("
        CREATE VIEW view_selected_certificate_assets
        AS
        SELECT
            ca.id,
            cr.real_estate_id,
            cap.id as property_id,
            p.name AS province, d.name AS district, w.name AS ward, s.name AS street,
            c.id AS certificate_id,c.certificate_num,
            TO_CHAR(c.certificate_date,'dd/mm/yyyy') AS certificate_date,
            c.document_num,
            TO_CHAR(c.document_date,'dd/mm/yyyy') AS document_date,
            c.status, c.sub_status, c.status_updated_at, c.service_fee, c.created_at,
            c.petitioner_name, c.petitioner_phone, c.petitioner_address,
            ct.name AS customer_name, ct.address AS customer_address,
            ct.phone AS customer_phone,dt.description AS customer_group_name,
            CASE WHEN cr.front_side = 1 then 'Hẻm' ELSE 'Mặt tiền' END AS front_side,
            cap.description AS location_description,
            COALESCE(t3.name,'unknown') AS appraiser_name,
            COALESCE(t4.name,'unknown') AS appraiser_sale_name,
            COALESCE(t5.name,'unknown') AS appraiser_perform_name,
            COALESCE(t6.name,'unknown') AS created_by,
            COALESCE(t7.name,'unknown') AS branch_name,
            ca.appraise_asset, ca.full_address,
            a_di.name AS appraise_method_used,
            CAST(cr.total_area AS BIGINT) AS land_area,
            CAST(cap_l_p.value AS BIGINT) AS land_price,
            CAST(cap_t_p.value AS BIGINT) AS tangible_price,
            CAST(cap_o_p.value AS BIGINT) AS other_asset_price,
            CAST(cr.total_price AS BIGINT) AS total_price
        FROM certificates c
            LEFT OUTER JOIN certificate_has_real_estates chr ON chr.certificate_id = c.id
            LEFT OUTER JOIN certificate_real_estates cr ON cr.id = chr.real_estate_id
            LEFT OUTER JOIN certificate_assets ca ON ca.real_estate_id = cr.id
            LEFT OUTER JOIN certificate_asset_properties cap ON cap.appraise_id = ca.id and cap.deleted_at IS NULL
            LEFT OUTER JOIN dictionaries dt ON dt.id = c.customer_group_id
            LEFT OUTER JOIN customers ct ON c.customer_id = ct.id
            LEFT OUTER JOIN appraisers t3 ON c.appraiser_id = t3.id AND t3.deleted_at IS NULL
            LEFT OUTER JOIN appraisers t4 ON c.appraiser_sale_id = t4.id AND t4.deleted_at IS NULL
            LEFT OUTER JOIN appraisers t5 ON c.appraiser_perform_id = t5.id AND t5.deleted_at IS NULL
            LEFT OUTER JOIN appraisers t6 ON c.created_by = t6.user_id AND t6.deleted_at IS NULL
            LEFT OUTER JOIN branches t7 ON c.branch_id = t7.id AND t7.deleted_at IS NULL
            LEFT OUTER JOIN provinces p ON ca.province_id = p.id
            LEFT OUTER JOIN districts d ON ca.district_id = d.id
            LEFT OUTER JOIN wards w ON ca.ward_id = w.id
            LEFT OUTER JOIN streets s ON ca.street_id = s.id
            LEFT OUTER JOIN appraise_other_information a_di ON ca.appraise_method_used_id = a_di.id
            LEFT OUTER JOIN certificate_asset_prices cap_t_p ON cap_t_p.appraise_id = ca.id AND cap_t_p.slug = 'tangible_asset_price' AND cap_t_p.deleted_at IS NULL
            LEFT OUTER JOIN certificate_asset_prices cap_l_p ON cap_l_p.appraise_id = ca.id AND cap_l_p.slug = 'land_asset_price' AND cap_l_p.deleted_at IS NULL
            LEFT OUTER JOIN certificate_asset_prices cap_o_p ON cap_o_p.appraise_id = ca.id AND cap_o_p.slug = 'other_asset_price' AND cap_o_p.deleted_at IS NULL
        WHERE c.deleted_at IS NULL
        ");

        DB::statement("
        CREATE VIEW view_selected_certificate_apartments
        AS 
        SELECT ca.apartment_asset_id AS real_estate_id,
            ca.id,
            cap.id AS property_id,
            p.name AS province,
            d.name AS district,
            w.name AS ward,
            s.name AS street,
            c.id AS certificate_id,
            c.certificate_num,
            to_char((c.certificate_date)::timestamp with time zone, 'dd/mm/yyyy'::text) AS certificate_date,
            c.document_num,
            to_char((c.document_date)::timestamp with time zone, 'dd/mm/yyyy'::text) AS document_date,
            c.status,
            c.sub_status,
            c.status_updated_at,
            c.service_fee,
            c.created_at,
            c.petitioner_name,
            c.petitioner_phone,
            c.petitioner_address,
            ct.name AS customer_name,
            ct.address AS customer_address,
            cap.description AS location_description,
            COALESCE(t3.name, 'unknown'::character varying) AS appraiser_name,
            COALESCE(t4.name, 'unknown'::character varying) AS appraiser_sale_name,
            COALESCE(t5.name, 'unknown'::character varying) AS appraiser_perform_name,
            COALESCE(t6.name, 'unknown'::character varying) AS created_by,
            COALESCE(t7.name, 'unknown'::text) AS branch_name,
            ca.appraise_asset,
            ca.full_address,
            a_di.name AS appraise_method_used,
            (cap_l_p.value)::bigint AS land_area,
            (cap_t_p.value)::bigint AS total_price,
            (cap_o_p.value)::bigint AS other_asset_price,
            cal.document_num AS gcn,
            (cap_d_p.value)::bigint AS land_price
        FROM ((((((((((((((((((((certificates c
            LEFT JOIN certificate_has_real_estates chr ON ((chr.certificate_id = c.id)))
            LEFT JOIN certificate_apartments ca ON ((ca.real_estate_id = chr.real_estate_id)))
            LEFT JOIN certificate_apartment_appraisal_base cr ON ((cr.apartment_asset_id = ca.id)))
            LEFT JOIN certificate_apartment_properties cap ON (((cap.apartment_asset_id = ca.apartment_asset_id) AND (cap.deleted_at IS NULL))))
            LEFT JOIN certificate_apartment_laws cal ON (((cal.apartment_asset_id = ca.id) AND (cal.deleted_at IS NULL))))
            LEFT JOIN customers ct ON ((c.customer_id = ct.id)))
            LEFT JOIN appraisers t3 ON (((c.appraiser_id = t3.id) AND (t3.deleted_at IS NULL))))
            LEFT JOIN appraisers t4 ON (((c.appraiser_sale_id = t4.id) AND (t4.deleted_at IS NULL))))
            LEFT JOIN appraisers t5 ON (((c.appraiser_perform_id = t5.id) AND (t5.deleted_at IS NULL))))
            LEFT JOIN appraisers t6 ON (((c.created_by = t6.user_id) AND (t6.deleted_at IS NULL))))
            LEFT JOIN branches t7 ON (((c.branch_id = t7.id) AND (t7.deleted_at IS NULL))))
            LEFT JOIN provinces p ON ((ca.province_id = p.id)))
            LEFT JOIN districts d ON ((ca.district_id = d.id)))
            LEFT JOIN wards w ON ((ca.ward_id = w.id)))
            LEFT JOIN streets s ON ((ca.street_id = s.id)))
            LEFT JOIN appraise_other_information a_di ON ((cr.method_used_id = a_di.id)))
            LEFT JOIN certificate_apartment_prices cap_t_p ON (((cap_t_p.apartment_asset_id = ca.id) AND ((cap_t_p.slug)::text = 'apartment_total_price'::text) AND (cap_t_p.deleted_at IS NULL))))
            LEFT JOIN certificate_apartment_prices cap_l_p ON (((cap_l_p.apartment_asset_id = ca.id) AND ((cap_l_p.slug)::text = 'apartment_area'::text) AND (cap_l_p.deleted_at IS NULL))))
            LEFT JOIN certificate_apartment_prices cap_o_p ON (((cap_o_p.apartment_asset_id = ca.id) AND ((cap_o_p.slug)::text = 'other_asset_price'::text) AND (cap_o_p.deleted_at IS NULL))))
            LEFT JOIN certificate_apartment_prices cap_d_p ON (((cap_d_p.apartment_asset_id = ca.id) AND ((cap_d_p.slug)::text = 'apartment_asset_price'::text) AND (cap_d_p.deleted_at IS NULL))))
        WHERE (c.deleted_at IS NULL);
        ");

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
    public function down() {}
}
