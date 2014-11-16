<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Announcement;
use app\models\Setting;
use app\modules\admin\models\AnnouncementSetupForm;
use app\modules\admin\models\AnnouncementForm;
use yii\web\NotFoundHttpException;
use yii\bootstrap\ActiveForm;

/* @var $form yii\bootstrap\ActiveForm */

class AnnouncementController extends Controller
{
	public function actionIndex() {
		$announments = Announcement::find()->all();
		
		return $this->render('index', array('announcements' => $announments));
	}
	
	public function actionSetup() {
		$model = new AnnouncementSetupForm();
		$announcementEnable = Setting::findOne(['name' => 'announcement_enable']);
	
		if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
			$announcementEnable->value = $model->enable;
				
			if ( $announcementEnable->save() ) {
				Yii::$app->session->setFlash('EnableAnnouncementSuccess');
			} else {
				Yii::$app->session->setFlash('EnableAnnouncementFail');
			}
				
			return $this->refresh();
		}
	
		$model->enable = $announcementEnable->value;
	
		return $this->render('setup', ['model' => $model]);
	}
	
	public function actionNew() {
		$model = new AnnouncementForm();
		
		if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
			$announcement = new Announcement();
			$announcement->title = $model->title;
			$announcement->mode_id = $model->modeId;
			$announcement->publish = $model->publish;
			$announcement->content = $model->content;
			
			if ( $announcement->save() ) {
				Yii::$app->session->setFlash('AddAnnouncementSuccess');
			} else {
				Yii::$app->session->setFlash('AddAnnouncementFail');
			}
			
			return $this->refresh();
		}
		
		return $this->render('new', array('model' => $model));
	}
	
	public function actionEdit( $id ) {
		$model = new AnnouncementForm();
		$announcement = Announcement::findOne(['id' => $id]);
		
		if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
			$announcement->title = $model->title;
			$announcement->mode_id = $model->modeId;
			$announcement->publish = $model->publish;
			$announcement->content = $model->content;
			
			if ( $announcement->save() ) {
				Yii::$app->session->setFlash('EditAnnouncementSuccess');
			} else {
				Yii::$app->session->setFlash('EditAnnouncementFail');
			}
			
			return $this->refresh();
		}

		$model->title = $announcement->title;
		$model->modeId = $announcement->mode_id;
		$model->publish = $announcement->publish;
		$model->content = $announcement->content;
		
		return $this->render('edit', array('model' => $model));
	}
	
	public function actionRemove( $id ) {
		$announcement = Announcement::findOne(['id' => $id]);
		
		if ( $announcement == null ) {
			throw new NotFoundHttpException;
		}
		
		return $announcement->delete();
	}
	
	public function actionPriority( $id ) {
		$announcement = Announcement::findOne(['id' => $id]);
		
		if ( $announcement == null ) {
			throw new NotFoundHttpException;
		}
		
		$priority = $announcement->mode_id;
		$announcement->mode_id = $priority > 0 ? 0 : 1; //If announcement mode is normal switch to important
		
		if ( !$announcement->save() ) {
			throw new BadRequestHttpException;
		}
		
		return $announcement->mode_id;
	}
	
	public function actionPublish( $id ) {
		$announcement = Announcement::findOne(['id' => $id]);
		
		if ( $announcement == null ) {
			throw new NotFoundHttpException;
		}
		
		$publish = $announcement->publish;
		$announcement->publish = $publish > 0 ? 0 : 1; //If announcement published, unpublish and otherwise
		
		if ( !$announcement->save() )
			throw new BadRequestHttpException;
		
		return $announcement->publish;
	}
}