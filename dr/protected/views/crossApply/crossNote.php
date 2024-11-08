<?php if (isset($typeText)&&$typeText=="dialog"): ?>
    <span>注：</span><br/>
    <span>1、如果是三方交叉收回承接方服务，业务场景请选择"资质借用”</span><br/>
    <span>2、申请类型为“合约金额调整”时，仅填变化值,即加做多少填多少，减做多少填多少</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;例:月金额加做100，则填100</span><br/>
    <span>3、服务合约做了金额调整之后再更换交叉场景(即增加新的子合约)，请注意月金额的填写</span>
<?php else:?>
    <span>注：</span><br/>
    <span>1、如果是三方交叉收回承接方服务，业务场景请选择"资质借用”</span><br/>
    <span>2、申请类型为“合约金额调整”时，仅填变化值,即加做多少填多少，减做多少填多少。例:月金额加做100，则填100</span><br/>
    <span>3、服务合约做了金额调整之后再更换交叉场景(即增加新的子合约)，请注意月金额的填写</span>
<?php endif ?>