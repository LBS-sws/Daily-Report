<div class="table-responsive">
    <table class="table table-fixed table-condensed table-bordered table-hover">
        <thead>
        <tr>
            <th width="50px">序号</th>
            <th width="80px">状态</th>
            <th width="90px">提交账号</th>
            <th width="90px">提交时间</th>
            <th width="110px">LBS审核账号</th>
            <th width="110px">LBS审核时间</th>
            <th width="145px">派单系统审核账号</th>
            <th width="145px">派单系统审核时间</th>
            <th width="90px">承接方城市</th>
            <th width="90px">承接方金额</th>
            <th width="90px">资质方城市</th>
            <th width="90px">资质方金额</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php
            $html = "";
            $crossTable = CrossApplyForm::getAllCrossListForTypeAndId($model->table_type,$model->service_id);
            if($crossTable){
                $i = 0;
                foreach ($crossTable as $row){
                    $i++;
                    $trClass = $model->id == $row["id"]?"success":"";
                    $html.="<tr class='{$trClass}'>";
                    $html.="<td>".$i."</td>";
                    $html.="<td>".CrossApplyList::getStatusStrForStatusType($row)."</td>";
                    $html.="<td>".$row["lcu"]."</td>";
                    $html.="<td>".$row["lcd"]."</td>";
                    $html.="<td>".$row["audit_user"]."</td>";
                    $html.="<td>".$row["audit_date"]."</td>";
                    $html.="<td>".$row["u_update_user"]."</td>";
                    $html.="<td>".$row["u_update_date"]."</td>";
                    $html.="<td>".(empty($row["cross_city"])?"":General::getCityName($row["cross_city"]))."</td>";
                    $html.="<td>".$row["cross_amt"]."</td>";
                    $html.="<td>".(empty($row["qualification_city"])?"":General::getCityName($row["qualification_city"]))."</td>";
                    $html.="<td>".$row["qualification_amt"]."</td>";
                    $html.="</tr>";
                }
            }
            echo $html;
            ?>
        </tr>
        </tbody>
    </table>
</div>