<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\AppraiseLaw;
use App\Models\AppraiseLawLandDetail;

class CreateAppraiseLawPurposeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraise_law_purpose_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law')
                ->onDelete('cascade');
			$table->integer('land_type_purpose_id')->nullable();
            $table->double('total_area')->nullable();
			$table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appraise_law_purpose_details');
    }
}
