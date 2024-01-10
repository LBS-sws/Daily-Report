<tr>
    <td><?php echo $this->record['id']; ?></td>
    <td><?php echo $this->record['info_type']; ?></td>
	<td class="text-break">
        <pre style="display: none;"><?php echo $this->record['data_content']; ?></pre>
        <span>查看</span>
    </td>
	<td class="text-break">
        <pre style="display: none;"><?php echo $this->record['out_content']; ?></pre>
        <span>查看</span>
    </td>
	<td><?php echo $this->record['message']; ?></td>
	<td><?php echo $this->record['lcu']; ?></td>
	<td><?php echo $this->record['lcd']; ?></td>
	<td><?php echo $this->record['lud']; ?></td>
	<td><?php echo $this->record['status_type']; ?></td>
	<td>
        <?php
        $url = Yii::app()->createUrl('curlNotes/send',array("index"=>$this->record['id']));
        echo TbHtml::link("重新发送",$url,array("class"=>"btn btn-default"));
        ?>
    </td>
</tr>
