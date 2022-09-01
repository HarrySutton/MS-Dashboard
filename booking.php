<div class="container-lg">
	<div class="row">
        <div class="col">
            <h1>Barn Booker</h1>
        </div>
    </div>
	<hr>
	<div class="row">
		<div class="col-4">
			<h2>Book Office</h2>
			<form method="POST">
				<table style="width: 100%">
					<tr>
						<td>Date</td>
						<td>
							<input type="date" name="datebook" id="date-book">
						</td>
					</tr>
					<tr>
						<td>Time</td>
						<td>
							<label><input type="radio" id="radio-morn" name="radiotime" value="morn" disabled> Morning</label><br>
							<label><input type="radio" id="radio-aftn" name="radiotime" value="aftn" disabled> Afternoon</label><br>
							<label><input type="radio" id="radio-aldy" name="radiotime" value="aldy" disabled> All Day</label><br>
							<label><input type="radio" id="radio-week" name="radiotime" value="week" disabled> Whole Week</label>
						</td>
					</tr>
					<tr>
						<td>Freelancer / Client</td>
						<td>
							<label><input type="checkbox" id="checkbox" name="checkbox"> Book on behalf of freelancer/client</label>
							<div id="freelancer-container" style="display: none;">
								<label>Name *</label><input type="text" name="textname"><br>
								<label>Email *</label><input type="text" name="textemail">
							</div>
						</td>
					</tr>
				</table>

				<button type="button" id="submit" style="width: 100%" disabled>Book</button><br>
				<div id="booking-message">
				</div>
				<div id="cancel-panel">
				</div>
				<div id="cancel-message"></div>
			</form>
		</div>
		<div class="col-8">
			<div class="hol-cal-wrap">
				<h2 id="date-heading"></h2>
				<div class="calendar-wrap" id="daycontainer">
					<h5>Please select a date</h5>
				</div>	
			</div>
		</div>
	</div>
	<br><br>
	<div class="hol-cal-wrap" style="width: 100%;">
		<div class="calendar-wrap" id="weekcontainer">
		</div>	
	</div>
</div>