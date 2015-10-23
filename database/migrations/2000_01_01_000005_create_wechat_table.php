<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//微信账号库
		Schema::create('wechat_accounts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 150)->comment = '公众号名称'; //名称
			$table->string('description', 255)->comment = '简介'; //简介
			$table->unsignedInteger('wechat_type')->default(0)->comment = '微信类型'; //微信类型
			$table->string('account', 100)->unique()->comment = '原始ID'; //原始ID
			$table->string('appid', 50)->unique()->comment = 'APP ID'; //appid
			$table->string('token', 150)->comment = 'TOKEN'; //token
			$table->string('appsecret', 100)->comment = 'APP Secret'; //appsecret
			$table->string('encodingaeskey', 100)->nullable()->comment = '加密KEY'; //encodingaeskey
			$table->unsignedInteger('qr_aid')->nullable()->comment = '二维码AID'; //二维码
			$table->timestamps();
		});
		//微信用户库
		Schema::create('wechat_users', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('waid')->comment = '公众号AccountID'; //account_id
			$table->string('openid', 150)->index()->comment = 'OpenID'; //OpenID
			$table->string('nickname', 50)->nullable()->comment = '昵称'; //昵称
			$table->unsignedInteger('gender')->nullable()->default(0)->comment = '性别'; //性别
			$table->unsignedInteger('avatar_aid')->nullable()->default(0)->comment = '头像AID'; //头像
			$table->tinyInteger('is_subscribed')->default(0)->comment = '是否关注'; //是否关注
			$table->string('country',50)->nullable()->comment = '国家'; //国家
			$table->string('province',50)->nullable()->comment = '省'; //省
			$table->string('city',50)->nullable()->comment = '市'; //市
			$table->string('language',20)->nullable()->comment = '语言'; //语言
			$table->string('unionid', 150)->nullable()->comment = '唯一ID'; //唯一ID
			$table->string('remark', 50)->nullable()->comment = '备注名'; //备注
			$table->unsignedInteger('groupid')->nullable()->default(0)->comment = '组ID'; //组ID
			$table->timestamp('subscribed_at')->nullable()->comment = '关注时间'; //关注时间
			$table->unsignedInteger('uid')->nullable()->default(0)->comment = '绑定的用户ID'; //绑定的用户ID
			$table->timestamps();

			$table->foreign('waid')->references('id')->on('wechat_accounts');
			$table->unique(['waid', 'openid']);
		});
		//微信素材库
		Schema::create('wechat_depots', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('waid')->comment = '公众号AccountID'; //account_id
			$table->enum('type', ['text','news','music','voice','image','video','shortvideo','callback'])->comment = '素材类型'; //素材类型
			$table->unsignedInteger('uid')->default(0)->comment = '用户ID'; //用户ID
			$table->timestamps();

			$table->foreign('waid')->references('id')->on('wechat_accounts');
		});
		//微信素材-文章 多图文
		Schema::create('wechat_depot_news', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title', 250)->comment = '标题'; //标题
			$table->string('author', 100)->comment = '作者'; //作者
			$table->string('description', 250)->comment = '简介'; //简介
			$table->unsignedInteger('cover_aid')->default(0)->comment = '封面图片AID'; //封面图片
			$table->tinyInteger('cover_in_content')->default(0)->comment = '是否将封面插入正文中'; //是否将封面插入正文中
			$table->text('content')->comment = '内容'; //内容
			$table->string('link', 250)->comment = 'URL链接'; //URL链接
			$table->timestamps();
		});
		//微信素材库 多图文关联表
		Schema::create('wechat_depot_news_relation', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('wdid')->comment = '素材库DepotID'; //素材库
			$table->unsignedInteger('wdnid')->comment = '文章NewsID'; //文章ID

			$table->foreign('wdid')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('wdnid')->references('id')->on('wechat_depot_news')
				->onUpdate('cascade')->onDelete('cascade');
			$table->unique(['wdid', 'wdnid']);
		});
		//微信素材-图片
		Schema::create('wechat_depot_images', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->unsignedInteger('aid')->comment = '图片AID'; //附件ID
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
		});
		//微信素材-音频
		Schema::create('wechat_depot_voices', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->unsignedInteger('aid')->comment = '音频AID'; //附件ID
			$table->string('format', 20)->comment = '音频格式'; //附件格式
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
		});
		//微信素材-视频
		Schema::create('wechat_depot_videos', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->string('title', 250)->comment = '标题'; //标题
			$table->string('description', 250)->comment = '简介'; //摘要
			$table->unsignedInteger('aid')->comment = '视频AID'; //附件ID
			$table->unsignedInteger('thunm_aid')->comment = '缩略图ID'; //缩略图附件ID
			$table->string('format', 20)->comment = '视频格式'; //附件格式
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
		});
		//微信素材-音乐
		Schema::create('wechat_depot_musics', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->unsignedInteger('aid')->comment = '音乐AID'; //附件ID
			$table->string('format', 20)->comment = '音乐格式'; //附件ID
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
		});
		//微信素材-文字
		Schema::create('wechat_depot_texts', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->text('content')->comment = '文字内容'; //文字内容
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
		});
		//微信素材-回调函数
		Schema::create('wechat_depot_callbacks', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->longText('callback')->comment = '回调函数'; //回调函数
			$table->longText('paramters')->comment = '回调参数'; //回调参数
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信菜单
		Schema::create('wechat_menus', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('waid')->index()->comment = '公众号AccountID'; //account id
			$table->unsignedInteger('pid')->default(0)->index()->comment = '父ID'; //pid
			$table->string('title', 50)->comment = '标题'; //标题
			$table->string('type', 50)->comment = '类型'; //类型
			$table->string('event', 50)->nullable()->comment = '事件名称'; //事件名称
			$table->string('event_key', 150)->nullable()->comment = '事件参数'; //事件参数
			$table->string('url', 250)->nullable()->comment = '网址'; //网址
			$table->unsignedInteger('wdid')->default(0)->comment = '素材库DepotID'; //素材库did
			
			//tree
			$table->unsignedInteger('order')->default(0)->index()->comment = 'TREE排序';
			$table->unsignedInteger('level')->default(0)->index()->comment = 'TREE等级';
			$table->string('path', '250')->index()->comment = 'TREE路径';

			$table->timestamps();

			$table->foreign('waid')->references('id')->on('wechat_accounts')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信消息
		Schema::create('wechat_messages', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('waid')->index()->comment = '公众号AccountID'; //account id
			$table->unsignedInteger('wuid')->index()->comment = '微信UID'; //user id
			$table->enum('transport_type', ['send', 'receive'])->default('receive')->index()->comment = '是发送还是接受'; //是发送还是接受
			$table->enum('type', ['depot', 'text', 'image', 'video', 'shortvideo', 'voice', 'link', 'location'])->index()->comment = '消息类型'; //类型
			$table->string('message_id', 100)->comment = '消息ID'; //消息ID
			$table->unsignedInteger('wdid')->default(0)->comment = '素材库DepotID'; //素材ID
			$table->tinyInteger('status')->default(0)->comment = '状态'; //状态
			$table->timestamps();
			$table->softDeletes(); //软删除

			$table->foreign('waid')->references('id')->on('wechat_accounts')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('wuid')->references('id')->on('wechat_users')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信消息-Link
		Schema::create('wechat_message_links', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->string('title', 100)->nullable()->comment = '标题'; //标题
			$table->string('description', 250)->nullable()->comment = '摘要'; //摘要
			$table->string('url', 250)->comment = '网址'; //网址
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_messages')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信消息-地理位置
		Schema::create('wechat_message_locations', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->decimal('x', 20, 6)->default(0)->comment = '经度';
			$table->decimal('y', 20, 6)->default(0)->comment = '纬度';
			$table->smallInteger('scale')->comment = '缩放比例';
			$table->string('precision', 100)->comment = '';
			$table->string('label', 250)->comment = '地址';
			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_messages')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信消息-图片/视频/音频
		Schema::create('wechat_message_media', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->string('format', 50)->nullable()->comment = '文件格式'; //文件格式
			$table->string('media_id', 250)->comment = '微信MediaID'; //微信Media ID
			$table->string('thumb_media_id', 250)->nullable()->comment = '缩略图MediaID'; //缩略图Media ID

			$table->unsignedInteger('aid')->default(0)->comment = '文件AID';
			$table->unsignedInteger('thumb_aid')->default(0)->comment = '缩略图AID';

			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_messages')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信消息-文字
		Schema::create('wechat_message_texts', function (Blueprint $table) {
			$table->unsignedInteger('id')->unique();
			$table->text('content')->comment = '文本内容'; //内容

			$table->timestamps();

			$table->foreign('id')->references('id')->on('wechat_messages')
				->onUpdate('cascade')->onDelete('cascade');
		});


		//微信自定义回复触发条件
		Schema::create('wechat_replies', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('waid')->index()->comment = '公众号AccountID'; //account id
			$table->enum('match_type', ['subscribe', 'whole', 'part'])->comment = '匹配类别';
			$table->string('keywords', 100)->nullable()->index()->comment = '关键词';
			$table->unsignedInteger('reply_count')->default(0)->comment = '全回复还是随机几条'; //随机返回几条

			$table->timestamps();

			$table->foreign('waid')->references('id')->on('wechat_accounts')
				->onUpdate('cascade')->onDelete('cascade');
		});

		//微信自定义回复-素材库-关联表
		Schema::create('wechat_reply_depot', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('wrid')->index()->comment = '回复ReplyID'; //reply id
			$table->unsignedInteger('wdid')->index()->comment = '素材库DepotID'; //素材ID

			$table->foreign('wrid')->references('id')->on('wechat_replies')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('wdid')->references('id')->on('wechat_depots')
				->onUpdate('cascade')->onDelete('cascade');

			$table->unique(['wrid', 'wdid']);
		});


		//微信账号库
		Schema::create('wechat_logs', function (Blueprint $table) {
			$table->increments('id');
			$table->text('url')->nullable()->comment = '来源网址'; //来源网址
			$table->text('log')->nullable()->comment = 'log日志内容'; //log日志内容
			$table->unsignedInteger('waid')->index()->comment = '素材库DepotID'; //素材ID
		
			$table->timestamps();

			$table->foreign('waid')->references('id')->on('wechat_accounts')
				->onUpdate('cascade')->onDelete('cascade');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('');
	}
}
