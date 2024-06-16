<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateViewSelectedAssetFollowStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS view_selected_certificate_assets");
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
                a_di.name appraise_method_used,
                CAST(cr.total_area AS BIGINT) AS land_area,
                CAST(cap_l_p.value AS BIGINT) AS land_price,
                CAST(cap_t_p.value AS BIGINT) AS tangible_price,
                CAST(cap_o_p.value AS BIGINT) AS other_asset_price,
                CAST(cr.total_price AS BIGINT) AS total_price
            FROM certificates c
                JOIN certificate_has_real_estates chr ON chr.certificate_id = c.id
                JOIN certificate_real_estates cr ON cr.id = chr.real_estate_id
                JOIN certificate_assets ca ON ca.real_estate_id = cr.id
                JOIN certificate_asset_properties cap ON cap.appraise_id = ca.id and cap.deleted_at IS NULL
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
