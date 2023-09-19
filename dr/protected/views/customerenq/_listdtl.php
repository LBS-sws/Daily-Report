<?php
	$withrow = $this->record['detailCount']>0;
	$idX = $this->record['company_id'];
?>
<tr>
	<td>
		<?php
			$iconX = $withrow ? "<span data-id='$idX' class='fa fa-plus-square'></span>" : "<span class='fa fa-square'></span>";
            $htmlOptions = $withrow ?array("class"=>"show-tr"):array();
			echo TbHtml::link($iconX, "javascript:void(0);",$htmlOptions);
		?>
	</td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['company_code']; ?></td>
	<td colspan=4><?php echo $this->record['company_name']; ?></td>
	<td><?php echo $this->record['company_status']; ?></td>
</tr>
