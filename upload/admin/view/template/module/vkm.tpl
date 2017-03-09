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
							<input name="vkm_app_id" placeholder="5874433" value="<?php echo (isset($vkm_app_id))?$vkm_app_id:'';?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" >
							VKAPISecret
						</label>
						<div class="col-sm-10">
							<input name="vkm_api_secret" placeholder="V0fuABf33SenDry98dOm" value="<?php echo (isset($vkm_api_secret))?$vkm_api_secret:'';?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" >
							VKAccessToken
						</label>
						<div class="col-sm-10">
							<input name="vkm_access_token" placeholder="bm1ca449383a951e5ea55aff0bd82f721e3467d9q6037a64601ad1c110b02bf5501c0fcaa39137743fe9b" value="<?php echo (isset($vkm_access_token))?$vkm_access_token:'';?>" class="form-control">
						</div>
					</div>
					<div class="form-group ">
						<label class="col-sm-1 control-label" >
							VKGroupName
						</label>
						<div class="col-sm-4">
							<?php if (isset($vkm_group_name)) {
								foreach ($vkm_group_name as $k=>$v) {?>
								<input name="vkm_group_name[]" value="<?php echo $v;?>" class="form-control"><br>
							<?php }
							}?>
							<input name="vkm_group_name[]" placeholder="VK API Change Log" class="form-control">
						</div>
						<label class="col-sm-1 control-label" >
							VKGroupID
						</label>
						<div class="col-sm-6">
							<?php if (isset($vkm_group_id)) {
								foreach ($vkm_group_id as $k=>$v) {?>
								<input name="vkm_group_id[]" value="<?php echo $v;?>" class="form-control"><br>
							<?php }
							} ?>
							<input name="vkm_group_id[]" placeholder="28551727" class="form-control">
						</div>
					</div>
					<hr>
					<h3>Обновление</h3>
					<div class="form-group">
						<div class="col-sm-10">
							<label class="col-sm-4 control-label"  for="input-status">
								Цена - <input name="vkm_fields_update[]" <?php echo ($vkm_fields_update['price'])?'checked':'';?> value="price" type="checkbox"><br>
								<span title="если остаток товара 0 - неактивный. если остаток больше 0 - активный">Активный/Неактивный</span>  - <input name="vkm_fields_update[]" <?=($vkm_fields_update['deleted'])?'checked':'';?> value="deleted" type="checkbox"><br>
							</label>
							<div class="col-sm-4"></div>
							<label class="col-sm-4 control-label"  for="input-status">
								При повторной выгрузке обновлять товар -
								<input name="vkm_not_duplication_product" <?php echo (isset($vkm_not_duplication_product))?'checked':'';?>  value="1" type="checkbox">
							</label>
							
						</div>
					</div>

				</form>
      </div>
    </div>
	</div>
</div>
<?php echo $footer; ?>