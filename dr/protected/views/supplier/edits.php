<form id="payreq-form" class="form-horizontal" action="/acct/index.php/payreq/edit?index=106" method="post">
    <section class="content-header">
        <h1>
            <strong>付款记录详情</strong>
        </h1>
        <!--
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#">Layout</a></li>
                <li class="active">Top Navigation</li>
            </ol>
        -->
    </section>
    <section class="content">
        <div class="box"><div class="box-body">
                <div class="btn-group" role="group">
                    <button class="btn btn-default" name="yt0" type="button" id="yt0" onclick="javascript:history.back(-1);"><span class="fa fa-reply"></span> 返回</button>
                </div>

            </div>
        </div>

        <div class="box box-info">
            <div class="box-body">
                <input name="PayReqForm[scenario]" id="PayReqForm_scenario" type="hidden" value="edit">			<input name="PayReqForm[id]" id="PayReqForm_id" type="hidden" value="106">			<input name="PayReqForm[status]" id="PayReqForm_status" type="hidden" value="Y">			<input name="PayReqForm[wfstatus]" id="PayReqForm_wfstatus" type="hidden" value="ED">			<input name="PayReqForm[req_user]" id="PayReqForm_req_user" type="hidden" value="amy.sh">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="PayReqForm_ref_no">参考编号</label>				<div class="col-sm-3">
                        <input readonly="readonly" name="PayReqForm[ref_no]" id="PayReqForm_ref_no" class="form-control readonly" type="text" value="<?php echo $model['ref_no']; ?>">				</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_req_dt">申请日期 <span class="required">*</span></label>				<div class="col-sm-3">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input class="form-control pull-right readonly" readonly="readonly" name="PayReqForm[req_dt]" id="PayReqForm_req_dt" type="text" value="<?php echo $model['req_dt']; ?>">					</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_trans_type_code">交易类别 <span class="required">*</span></label>				<div class="col-sm-7">
                        <input name="PayReqForm[trans_type_code]" id="PayReqForm_trans_type_code" type="hidden" value="BANKOUT">					<input readonly="readonly" class="form-control readonly" type="text" value="<?php echo $model['trans_type_code']; ?>" name="trans_type_desc" id="trans_type_desc">				</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_acct_id">付款账户 <span class="required">*</span></label>				<div class="col-sm-7">
                        <input name="PayReqForm[acct_id]" id="PayReqForm_acct_id" type="hidden" value="29">					<input readonly="readonly" class="form-control readonly" type="text" value="<?php echo $model['acct_code_desc']; ?>" name="acct_name" id="acct_name">				</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_payee_name">收款人 <span class="required">*</span></label>				<div class="col-sm-2">
                        <input name="PayReqForm[payee_type]" id="PayReqForm_payee_type" type="hidden" value="S">					<input readonly="readonly" class="form-control readonly" type="text" value="<?php if($model['payee_type']=='S'){echo "供应商";} ?>" name="payee_type_name" id="payee_type_name">				</div>
                    <div class="col-sm-7">
                        <div class="input-group"><input maxlength="500" readonly="readonly" name="PayReqForm[payee_name]" id="PayReqForm_payee_name" class="input-60 form-control readonly" type="text" value="<?php echo $model['payee_name']; ?>"><span class="input-group-btn"><button name="btnPayee" id="btnPayee" class="btn btn-default disabled" disabled="disabled" type="button"><span class="fa fa-search"></span> 收款人</button></span></div><input name="PayReqForm[payee_id]" id="PayReqForm_payee_id" type="hidden" value="4">				</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_pitem_desc">出纳申请项目 <span class="required">*</span></label>				<div class="col-sm-7">
                        <input name="PayReqForm[item_code]" id="PayReqForm_item_code" type="hidden" value="CO0003"><div class="input-group"><input maxlength="500" readonly="readonly" name="PayReqForm[pitem_desc]" id="PayReqForm_pitem_desc" class="form-control readonly" type="text" value="<?php echo $model['item_name']."(".$model['item_code'].")"; ?> "><span class="input-group-btn"><button name="btnPaidItem" id="btnPaidItem" class="btn btn-default disabled" disabled="disabled" type="button"><span class="fa fa-search"></span> 出纳申请项目</button></span></div>				</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_acct_code">会计编码 <span class="required">*</span></label>				<div class="col-sm-4">
                        <input name="PayReqForm[acct_code]" id="PayReqForm_acct_code" type="hidden" value="1405"><input maxlength="500" readonly="readonly" name="PayReqForm[acct_code_desc]" id="PayReqForm_acct_code_desc" class="form-control readonly" type="text" value="<?php echo $model['acct_code'];echo "&nbsp;&nbsp;&nbsp;&nbsp;".$model['acct_name']; ?>">				</div>

                    <label class="col-sm-2 control-label" for="PayReqForm_int_fee">综合费用</label>				<div class="col-sm-1">
                        <input name="PayReqForm[int_fee]" id="PayReqForm_int_fee" type="hidden" value="N"><input readonly="readonly" class="form-control readonly" type="text" value="<?php echo $model['int_fee']; ?>" name="int_fee" id="int_fee">				</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="PayReqForm_item_desc">摘要</label>				<div class="col-sm-7">
                        <textarea rows="3" cols="60" maxlength="1000" readonly="readonly" class="form-control readonly" name="PayReqForm[item_desc]" id="PayReqForm_item_desc"><?php echo $model['item_desc']; ?></textarea>				</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label required" for="PayReqForm_amount">金額 <span class="required">*</span></label>				<div class="col-sm-3">
                        <div class="input-group"><span class="input-group-addon"><span class="fa fa-money"></span></span><input min="0" readonly="readonly" name="PayReqForm[amount]" id="PayReqForm_amount" class="input-10 form-control readonly" type="number" value="<?php echo $model['amount']; ?>"></div>				</div>
                </div>

            </div>
        </div>
    </section>






</form>