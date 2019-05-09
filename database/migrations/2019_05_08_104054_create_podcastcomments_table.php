<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodcastcommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcastcomments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('podcast_id');
            $table->string('author_name');
            $table->string('author_email');
            $table->text('comment');
            $table->tinyInteger('is_deleted')->default("1")->comment('1=not deleted,0=deleted');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('podcastcomments');
    }
}
