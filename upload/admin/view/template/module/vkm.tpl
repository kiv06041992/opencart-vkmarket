<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
	<button type="submit" form="form-affiliate" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
	<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
	<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	<?php } ?>
      </ul>
    </div>
  </div>
	<div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
		<div class="panel panel-default">
      <div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
       <h3>SETTINGS_VK_API</h3>
				<form action="<?php echo $action;?>" method="post" id="form-settings" class="form-horizontal" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status">
							VKAPPID
						</label>
						<div class="col-sm-10">
							<input name="vkm_app_id" value="<?php echo $vkm_app_id;?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" >
							VKAPISecret
						</label>
						<div class="col-sm-10">
							<input name="vkm_api_secret" value="<?php echo $vkm_api_secret;?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" >
							VKAccessToken
						</label>
						<div class="col-sm-10">
							<input name="vkm_access_token" value="<?php echo $vkm_access_token;?>" class="form-control">
						</div>
					</div>
					<div class="form-group ">
						<label class="col-sm-1 control-label" >
							VKGroupName
						</label>
						<div class="col-sm-4">
							<?php foreach ($vkm_group_name as $k=>$v) {?>
								<input name="vkm_group_name[]" value="<?php echo $v;?>" class="form-control"><br>
							<?php }?>
							<input name="vkm_group_name[]" value="" class="form-control">
						</div>
						<label class="col-sm-1 control-label" >
							VKGroupID
						</label>
						<div class="col-sm-6">
							<?php foreach ($vkm_group_id as $k=>$v) {?>
								<input name="vkm_group_id[]" value="<?php echo$v;?>" class="form-control"><br>
							<?php }?>
							<input name="vkm_group_id[]" value="" class="form-control">
						</div>
						
					</div>
				</form>
      </div>
    </div>
	</div>
</div>
<?php echo $footer; ?>