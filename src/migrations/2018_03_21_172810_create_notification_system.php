<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_system', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('main_id')->default(0)->comment('通知类型 0:全体 1:主体ID');
            $table->string('title', 255)->comment('标题');
            $table->text('content', 255)->comment('内容');
            $table->unsignedInteger('views')->default(0)->comment('阅读量');
            $table->unsignedInteger('weight')->default(0)->comment('权重');
            $table->tinyInteger('is_delete')->default(0)->comment('是否删除 0:否 1:是');
            $table->timestamps();
            $table->engine = 'MyISAM';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_system');
    }
}
