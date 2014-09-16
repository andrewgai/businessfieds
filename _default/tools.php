<?	
	
	$title = "Tools";
	include("head.php"); ?>

	<div id="content">
		
		<div class="content-menu side-nav floatright" style="width: 220px">
			<ul class="first">
				<li class="active">Tools</li>
				<li class="constant"><a href="#finance_calculator">Finance Calculator</a></li>
			</ul>
  		</div><!-- sidebar content-box -->
    	
    	<div class="content-box panels" style="width: 760px;">

	    	<div id="finance_calculator" class="panel">
		    	<h2>Financing Calculator</h2>
				<div class="lightform grid" id="fc" style="margin-top:30px;float:left">
					<label for="sale_price">Sale Price</label>
					<input type="text" class="hashelp" id="fc_saleprice" name="sale_price" style="width:172px;"><span class="helptext">$</span>
					
					<label for="down_paymt">Down Payment (%)</label>
					<select id="fc_downpayment" class="wide">
						<option value="0">0%</option>
						<option value="0.10">10%</option>
						<option value="0.15">15%</option>
						<option value="0.20">20%</option>
						<option value="0.25">25%</option>
						<option value="0.30">30%</option>
						<option value="0.40">40%</option>
						<option value="0.50">50%</option>
						<option value="0.60">60%</option>
						<option value="0.70">70%</option>
						<option value="0.80">80%</option>
					</select>
					
					
					<label for="rate">Interest Rate</label>
					<input type="text" name="rate" id="fc_rate" class="hashelp" style="width:117px"><span class="helptext">%</span>
					
					<label for="years">Term</label>
					<input type="number" id="fc_years" min="0" max="30" name="years" class="hashelp" style="width:97px"><span class="helptext">years</span>
					
					<label for="paymt">Monthly Payment</label>
					<input type="text" id="fc_payment" name="paymt" class="wide" readonly="readonly">
				</div>
				
				<div style="float:left;margin-top:30px;width:300px">
					text of stuff text of stuff text of stuff text of stufftext of stuff text of stufftext of stuff text of stufftext of stuff text of stuff
				</div>
				<br>
				<br style="clear:both">
			</div>

			
		
    	</div><!-- main_page content-box -->
    	<br style="clear:both">
    </div>
    <script>
		$(document).ready(function() {
			$('li.active').siblings().slideDown();
			$('#fcalc_link').click(function(e) {
				e.preventDefault();
				$('#finance_calculator').slideDown();
				faderOn();
				return false;
			});
			$('#fc input').keyup(function() {
				fcCalculate();
			});
			$('#fc select').change(function() {
				fcCalculate();
			});
			$('#fc_saleprice').blur(function() {
				
				if ( isNumber($('#fc_saleprice').val()) ) {
					$('#fc_downpayment option').each(function(event) {
						//alert(this.text + ' ' + this.value);
						var salePrice = $('#fc_saleprice').val();
						var financed = $(this).val()*salePrice;
						$(this).text($(this).val()*100 + '% ($' + (digits(financed.toFixed(2))) + ')');
						//$(this).text('helloooo');
					})
					
				}
			});
		});
	</script>
    <? include("foot.php"); ?>