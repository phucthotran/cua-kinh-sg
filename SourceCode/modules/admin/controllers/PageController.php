<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Page;
use app\modules\admin\models\PageForm;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\models\Setting;

class PageController extends Controller
{	
    public function actionIndex() {
    	$pages = Page::find()->all();
    	
        return $this->render( 'index', array( 'pages' => $pages ) );
    }
    		
    public function actionNew() {    	
    	$model = new PageForm();    	

    	if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
    		$page = new Page();
    		$page->title = $model->title;
    		$page->url = $model->url;
    		$page->keywords = $model->keywords;
    		$page->publish = $model->publish;
    		$page->content = $model->content;
    		
    		if( $page->save() ) {
    			Yii::$app->session->setFlash( 'Done' );
    		} else {
    			Yii::$app->session->setFlash( 'Fail' );
    		}
    		
    		return $this->refresh();
    	}    	
    	
    	return $this->render('new', array( 'model' => $model ) );
    }

    public function actionEdit( $id ) {
    	$model = new PageForm();
    	$page = Page::findOne( $id );
    	
    	if ( $page == null ) {
    		throw new NotFoundHttpException;
    	}

    	if ( $model->load( Yii::$app->request->post() ) && $model->validate() )	{
    		$page->title = $model->title;
    		$page->url = $model->url;
    		$page->keywords = $model->keywords;
    		$page->publish = $model->publish;
    		$page->content = $model->content;
    		
    		if( $page->save() ) {
    			Yii::$app->session->setFlash( 'Done' );
    		} else {
    			Yii::$app->session->setFlash( 'Fail' );
    		}
    		
    		return $this->refresh();
    	}

    	$model->title = $page->title;
    	$model->url = $page->url;
    	$model->keywords = $page->keywords;
    	$model->publish = $page->publish;
    	$model->content = $page->content;
    	
    	return $this->render('edit', array( 'model' => $model ) );
    }
    
    public function actionRemove( $id )
    {
    	$page = Page::findOne( $id );
    	
    	if ( $page == null ) {
    		throw new NotFoundHttpException;
    	}
		
    	return $page->delete();
    }
    
    public function actionPublish( $id )
    {
    	$page = Page::findOne( $id );
    	
    	if ( $page == null ) {
    		throw new NotFoundHttpException;
    	}
    	
    	$publish = $page->publish;
    	$page->publish = $publish == 1 ? 0 : 1; //If page published, unpublish and otherwise
    	
    	if ( !$page->save() ) {
    		throw new BadRequestHttpException;
    	}
    	    	    	
    	return $page->publish;
    }
    
    public function actionHomepage( $id ) {
    	$homepage = Setting::findOne( ['name' => 'homepage_id'] );

    	if ( $homepage == null ) {
    		throw new BadRequestHttpException;
    	}
    	
    	$homepage->value = $id;
    	
    	return $homepage->save();
    }
}
