<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\AppraiseLaw;
use App\Models\AppraiseLawLandDetail;

class CreateAppraiseLawLandDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraise_law_land_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law')
                ->onDelete('cascade');
			$table->integer('doc_no')->nullable();
            $table->integer('land_no')->nullable();
			$table->integer('doc_no_old')->nullable();
            $table->integer('land_no_old')->nullable();
			$table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
		
		foreach(AppraiseLaw::withTrashed()->get() as $item) {
			AppraiseLawLandDetail::create([
				'appraise_law_id' => $item->id,
				'doc_no' => $item->doc_no,
				'land_no' => $item->land_no,
			]);
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appraise_law_land_details');
    }
}
