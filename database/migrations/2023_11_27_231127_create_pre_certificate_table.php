<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_certificates', function (Blueprint $table) {
            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2021_12_08_231456_create_certificate_table.php
            $table->increments('id');
            $table->integer('status');
            $table->integer('ticket_num')->nullable();
			$table->string('document_num')->nullable();
			$table->date('document_date')->nullable();
            $table->string('certificate_num')->nullable();
            $table->date('certificate_date')->nullable();
            $table->string('petitioner_name')->nullable();
            $table->string('petitioner_phone')->nullable();
            $table->string('petitioner_address')->nullable();
            $table->string('address')->nullable();

            $table->integer('appraiser_id')->nullable();
            $table->foreign('appraiser_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraiser_manager_id')->nullable();
            $table->foreign('appraiser_manager_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraiser_confirm_id')->nullable();
            $table->foreign('appraiser_confirm_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraise_purpose_id');
            $table->foreign('appraise_purpose_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->text('document_description');

            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            $table->date('appraise_date')->nullable();

            // path 2022_07_27_062805_update_cerificate_second_table.php
		    $table->integer('service_fee')->unsigned()->default(0)->before('created_at');
			// $table->string('appraiser_sale_id')->nullable()->before('created_at');
			// $table->string('appraiser_perform_id')->nullable()->before('created_at');
			
            // path H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_01_085158_update_cerificate_third_table.php
            $table->integer('customer_id')->nullable()->before('created_at');
        
            // path H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_02_023642_update_cerificate_fourth_table.php
			// $table->string('appraiser_sale_id')->change();
			// $table->string('appraiser_perform_id')->change();
			$table->text('document_description')->nullable()->change();
            
            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_02_035246_update_cerificate_firth_table.php
		    $table->timestamp('status_updated_at')->useCurrent();
           
            //H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_04_085208_change_string_to_integer_certificate_table.php
            $table->integer('appraiser_sale_id')->nullable();
            $table->integer('appraiser_perform_id')->nullable();

            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_09_085414_add_column_commission_fee_certificates_table.php
		    $table->float('commission_fee')->unsigned()->default(0)->before('created_at');

            //H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_15_092548_add_column_note_in_certificates_table.php
			$table->text('note')->unsigned()->nullable()->before('created_at');
			$table->integer('duration_time')->unsigned()->nullable()->before('created_at');
        
            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_17_030949_add_column_status_expired_at_certificates_table.php
			$table->dateTime('status_expired_at')->nullable()->before('created_at');
        
            //H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_09_29_093528_add_column_detail_type_certificates_table.php
			$table->json('document_type')->nullable()->before('created_at');
        
            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_10_19_094922_add_column_branch_id_certificates_table.php
            $table->integer('branch_id')->unsigned()->nullable();
        
            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_10_31_064649_add_column_identity_card_to_certificates_table.php
            $table->string('petitioner_identity_card')->nullable();

            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_11_25_014156_add_sub_status_to_certificates_table.php
            $table->integer('sub_status')->default(1);
       
            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_12_02_100659_update_certificate_service_fee.php
            DB::statement("DROP MATERIALIZED VIEW IF EXISTS view_certificate_briefs");

            if (Schema::hasColumn('pre_certificates', 'service_fee')) {
                Schema::table('pre_certificates', function (Blueprint $table) {
                    $table->bigInteger('service_fee')->default(0)->change();
                });
            }

            DB::statement("
            create MATERIALIZED VIEW view_certificate_briefs
                AS
                    select t1.id, t1.created_at, t1.updated_at, t1.status,
                        case t1.status
                            when 1 then 'Mới'
                            when 2 then 'Đang thẩm định'
                            when 3 then 'Đang duyệt'
                            when 4 then 'Hoàn thành'
                            else 'Hủy'
                        end as status_text,
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
                    from pre_certificates t1
                        left outer join (select tt1.id , tt2.description
                                        from pre_certificates tt1 inner join certificate_dictionaries tt2 on tt2.type ='PROCESSING_TIME'
                                        and (case tt1.status
                                                when 1 then 'MOI'
                                                when 2 then 'DANG-THAM-DINH'
                                                when 3 then 'DANG-DUYET'
                                                end ) = tt2.acronym and tt2.status =1
                                        and tt1.deleted_at is null
                                        ) t2 on t1.id = t2.id
                        left outer join appraisers t3 on t1.appraiser_id = t3.id
                        left outer join appraisers t4 on t1.appraiser_sale_id = t4.id
                        left outer join appraisers t5 on t1.appraiser_perform_id = t5.id
                        left outer join appraisers t6 on t1.created_by = t6.user_id
                        left outer join branches t7 on t1.branch_id = t7.id
                        left outer join branches t8 on t6.branch_id = t8.id

                    where t1.deleted_at is null
            ");

            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2023_02_22_083213_change_customer_id_null_certificates_table.php
            $table->integer('customer_id')->nullable()->change();

            // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2023_11_14_151641_add_petitioner_birthday_certificate.php
            $table->date('petitioner_birthday')->nullable()->before('created_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_certificates');

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_09_085414_add_column_commission_fee_certificates_table.php
        if (Schema::hasColumn('certificates', 'commission_fee')){
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('commission_fee');
            });
        }

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_15_092548_add_column_note_in_certificates_table.php
        if (Schema::hasColumn('pre_certificates', 'note')){
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasColumn('pre_certificates', 'duration_time')){
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('duration_time');
            });
        }

        //H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_08_17_030949_add_column_status_expired_at_certificates_table.php
        if (Schema::hasColumn('pre_certificates', 'status_expired_at')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
				$table->dropColumn('status_expired_at');
            });
        }
   
        //H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_09_29_093528_add_column_detail_type_certificates_table.php
        if (Schema::hasColumn('pre_certificates', 'document_type')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
				$table->dropColumn('document_type');
            });
        }

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_10_19_094922_add_column_branch_id_certificates_table.php
        if (Schema::hasColumn('pre_certificates', 'branch_id')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_10_31_064649_add_column_identity_card_to_certificates_table.php
        if (!Schema::hasColumn('pre_certificates', 'petitioner_identity_card')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('petitioner_identity_card');
            });
        }

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_11_25_014156_add_sub_status_to_certificates_table.php
         if (Schema::hasColumn('pre_certificates', 'sub_status')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2022_12_02_100659_update_certificate_service_fee.php
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS view_certificate_briefs");

        if (!Schema::hasColumn('pre_certificates', 'service_fee')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->integer('service_fee')->default(0)->change();
            });
        }

        DB::statement("
        create MATERIALIZED VIEW view_certificate_briefs
            AS
                select t1.id, t1.created_at, t1.updated_at, t1.status,
                    case t1.status
                        when 1 then 'Mới'
                        when 2 then 'Đang thẩm định'
                        when 3 then 'Đang duyệt'
                        when 4 then 'Hoàn thành'
                        else 'Hủy'
                    end as status_text,
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
                from pre_certificates t1
                    left outer join (select tt1.id , tt2.description
                                    from pre_certificates tt1 inner join certificate_dictionaries tt2 on tt2.type ='PROCESSING_TIME'
                                    and (case tt1.status
                                            when 1 then 'MOI'
                                            when 2 then 'DANG-THAM-DINH'
                                            when 3 then 'DANG-DUYET'
                                            end ) = tt2.acronym and tt2.status =1
                                    and tt1.deleted_at is null
                                    ) t2 on t1.id = t2.id
                    left outer join appraisers t3 on t1.appraiser_id = t3.id
                    left outer join appraisers t4 on t1.appraiser_sale_id = t4.id
                    left outer join appraisers t5 on t1.appraiser_perform_id = t5.id
                    left outer join appraisers t6 on t1.created_by = t6.user_id
                    left outer join branches t7 on t1.branch_id = t7.id
                    left outer join branches t8 on t6.branch_id = t8.id

                where t1.deleted_at is null
        ");

        // H:\works\fastvalue\F-Value-Pro-Backend\database\migrations\2023_11_14_151641_add_petitioner_birthday_certificate.php
        if (!Schema::hasColumn('pre_certificates', 'petitioner_birthday')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('petitioner_birthday');
            });
        }
    }
}
