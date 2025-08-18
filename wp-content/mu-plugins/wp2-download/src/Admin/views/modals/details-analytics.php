<!-- Analytics Details Modal -->
<div class="modal fade" id="posthogDetailsModal" tabindex="-1" aria-labelledby="posthogDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="posthogDetailsModalLabel">Analytics Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="modal-content-body">
					<h6 class="fw-semibold mt-2">PostHog Summary</h6>
					<p><strong>Project API:</strong> <span class="code">https://app.posthog.com</span></p>
					<p><strong>Project API Key:</strong> <span class="code">phc_xxxx</span></p>
					<hr>
					<h6 class="fw-semibold mt-4">Connected Sites</h6>
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead>
								<tr>
									<th>Site URL</th>
									<th>Last Event</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><a href="https://site-a.example.com"
											target="_blank">https://site-a.example.com</a></td>
									<td>8/16/2025, 10:30:00 AM</td>
								</tr>
								<tr>
									<td><a href="https://site-b.example.com"
											target="_blank">https://site-b.example.com</a></td>
									<td>8/15/2025, 6:00:00 PM</td>
								</tr>
							</tbody>
						</table>
					</div>
					<h6 class="fw-semibold mt-4">Last Ingested Event</h6>
					<p><strong>Event:</strong> <span class="badge text-bg-info">$pageview</span></p>
					<p><strong>Timestamp:</strong> 8/16/2025, 10:30:00 AM</p>
					<p><strong>Distinct ID:</strong> user_1234</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>