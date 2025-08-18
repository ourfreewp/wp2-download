document.addEventListener('DOMContentLoaded', function () {
	function testConnection(service, statusId) {
		const statusEl = document.getElementById(statusId);
		if (statusEl) {
			statusEl.textContent = 'Testing...';
		}
		fetch('/wp-json/wp2-download/v1/test-connection', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': (window.wpApiSettings && window.wpApiSettings.nonce) ? window.wpApiSettings.nonce : ''
			},
			body: JSON.stringify({ service })
		})
			.then(res => res.json())
			.then(data => {
				if (statusEl) {
					statusEl.textContent = data.message || (data.ok ? 'Connection successful.' : 'Connection failed.');
					statusEl.style.color = data.ok ? 'green' : 'red';
				}
			})
			.catch(() => {
				if (statusEl) {
					statusEl.textContent = 'Error testing connection.';
					statusEl.style.color = 'red';
				}
			});
	}

	const tests = [
		{ btn: 'test-storage-connection', service: 'storage', status: 'storage-connection-status' },
		{ btn: 'test-dev-connection', service: 'development', status: 'dev-connection-status' },
		{ btn: 'test-licensing-connection', service: 'licensing', status: 'licensing-connection-status' },
		{ btn: 'test-analytics-connection', service: 'analytics', status: 'analytics-connection-status' }
	];

	tests.forEach(({ btn, service, status }) => {
		const el = document.getElementById(btn);
		if (el) {
			el.addEventListener('click', function () {
				testConnection(service, status);
			});
		}
	});
});
document.addEventListener('DOMContentLoaded', () => {
	// Handle manual refresh button click
	document.addEventListener('click', function (e) {
		// Health check
		if (e.target.classList.contains('wp2-run-health-check')) {
			const btn = e.target;
			const originalText = btn.textContent;
			btn.textContent = 'Running...';
			btn.disabled = true;
			fetch(wp2Hub.apiUrl + 'run_health_check', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': wp2Hub.ajaxNonce
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					slug: btn.dataset.postid,
					nonce: wp2Hub.ajaxNonce
				})
			}).then(res => res.json()).then(data => {
				if (data.success) {
					location.reload();
				} else {
					btn.textContent = 'Error!';
					setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 2000);
				}
			});
		}
		// Sync to R2
		if (e.target.classList.contains('wp2-sync-r2')) {
			const btn = e.target;
			const originalText = btn.textContent;
			btn.textContent = 'Syncing...';
			btn.disabled = true;
			fetch(wp2Hub.apiUrl + 'sync_to_r2', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': wp2Hub.ajaxNonce
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					slug: btn.dataset.slug,
					nonce: wp2Hub.ajaxNonce
				})
			}).then(res => res.json()).then(data => {
				if (data.success) {
					location.reload();
				} else {
					btn.textContent = 'Error!';
					setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 2000);
				}
			});
		}
		// Create Release
		if (e.target.classList.contains('wp2-create-release')) {
			const btn = e.target;
			const originalText = btn.textContent;
			btn.textContent = 'Creating...';
			btn.disabled = true;
			fetch(wp2Hub.apiUrl + 'create_release', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': wp2Hub.ajaxNonce
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					slug: btn.dataset.slug,
					nonce: wp2Hub.ajaxNonce
				})
			}).then(res => res.json()).then(data => {
				if (data.success) {
					location.reload();
				} else {
					btn.textContent = 'Error!';
					setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 2000);
				}
			});
		}
		// Edit Manifest
		if (e.target.classList.contains('wp2-edit-manifest')) {
			const btn = e.target;
			const originalText = btn.textContent;
			btn.textContent = 'Editing...';
			btn.disabled = true;
			fetch(wp2Hub.apiUrl + 'edit_manifest', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': wp2Hub.ajaxNonce
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					slug: btn.dataset.slug,
					nonce: wp2Hub.ajaxNonce
				})
			}).then(res => res.json()).then(data => {
				if (data.success) {
					location.reload();
				} else {
					btn.textContent = 'Error!';
					setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 2000);
				}
			});
		}
		// Purge Artifact
		if (e.target.classList.contains('wp2-purge-artifact')) {
			const btn = e.target;
			const originalText = btn.textContent;
			btn.textContent = 'Purging...';
			btn.disabled = true;
			fetch(wp2Hub.apiUrl + 'purge_artifact', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': wp2Hub.ajaxNonce
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					slug: btn.dataset.slug,
					nonce: wp2Hub.ajaxNonce
				})
			}).then(res => res.json()).then(data => {
				if (data.success) {
					location.reload();
				} else {
					btn.textContent = 'Error!';
					setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 2000);
				}
			});
		}
	});

	// Handle Refresh All button
	document.addEventListener('click', function (e) {
		if (e.target.classList.contains('wp2-run-all-checks')) {
			const btn = e.target;
			const originalText = btn.textContent;
			btn.textContent = 'Running...';
			btn.disabled = true;
			fetch(wp2Hub.apiUrl + 'run_all_health_checks', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': wp2Hub.ajaxNonce
				},
				credentials: 'same-origin',
				body: JSON.stringify({
					nonce: wp2Hub.ajaxNonce
				})
			}).then(res => res.json()).then(data => {
				if (data.success) {
					location.reload();
				} else {
					btn.textContent = 'Error!';
					setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 2000);
				}
			});
		}
	});

	// Handle manifest purge button
	const purgeBtn = document.getElementById('wp2-purge-manifests');
	if (purgeBtn) {
		purgeBtn.addEventListener('click', function () {
			if (confirm('Are you sure you want to purge processed manifests? This will force all manifests to be re-ingested.')) {
				fetch(wp2Hub.apiUrl + 'purge_processed_manifests', {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'X-WP-Nonce': wp2Hub.ajaxNonce,
						'Content-Type': 'application/json'
					}
				})
					.then(res => res.json().catch(() => ({ success: false, message: 'Invalid JSON response' })))
					.then(data => {
						if (data.success) {
							alert(data.message);
							location.reload();
						} else {
							let msg = 'Failed to purge manifests: ' + (data.message || 'Unknown error');
							if (msg.match(/cookie|auth|nonce|login/i)) {
								msg += '\n\nMake sure you are logged in as an admin and your browser is sending authentication cookies.';
							}
							alert(msg);
						}
					})
					.catch(err => {
						alert('Error: ' + err + '\n\nMake sure you are logged in and cookies are sent.');
					});
			}
		});
	}

	// Handle package details modal
	const packageDetailsModal = document.getElementById('packageDetailsModal');
	packageDetailsModal.addEventListener('show.bs.modal', (event) => {
		const button = event.relatedTarget;
		const packageData = JSON.parse(button.getAttribute('data-package'));

		// Set modal title
		document.getElementById('packageDetailsModalLabel').innerText = packageData.name;

		// Populate summary
		document.getElementById('detailsName').innerText = packageData.name;
		document.getElementById('detailsType').innerText = packageData.type;
		document.getElementById('detailsSlug').innerText = packageData.slug;
		document.getElementById('detailsVersion').innerText = packageData.latest_version || 'N/A';
		document.getElementById('detailsLastTag').innerText = (packageData.github_data && packageData.github_data.latest_tag) ? packageData.github_data.latest_tag : 'N/A';

		let healthBadgeClass = '';
		if (packageData.health === 'Healthy') healthBadgeClass = 'text-bg-success';
		else if (packageData.health === 'Pending Release') healthBadgeClass = 'text-bg-warning';
		else if (packageData.health === 'Audit Warning') healthBadgeClass = 'text-bg-danger';
		document.getElementById('detailsHealth').innerHTML = `<span class="badge ${healthBadgeClass}">${packageData.health}</span>`;

		// Populate API endpoints
		const apiBaseUrl = (typeof wp2Hub !== 'undefined' && wp2Hub.apiUrl) ? wp2Hub.apiUrl : '';
		document.getElementById('endpointLatest').innerText = `GET ${apiBaseUrl}packages/${packageData.type.toLowerCase()}/${packageData.slug}`;
		document.getElementById('endpointDownload').innerText = `GET ${apiBaseUrl.replace(/\/wp-json\/wp2\/v1\/$/, '/wp2-download/')}${packageData.type.toLowerCase()}/${packageData.slug}/${packageData.latest_version}`;
		document.getElementById('endpointHealthCheck').innerText = `POST ${apiBaseUrl}packages/${packageData.type.toLowerCase()}/${packageData.slug}/run-health-check`;

		// Populate Release History
		const releaseHistoryTableBody = document.getElementById('releaseHistoryTableBody');
		releaseHistoryTableBody.innerHTML = '';
		if (packageData.r2_data && Array.isArray(packageData.r2_data.releases) && packageData.r2_data.releases.length > 0) {
			packageData.r2_data.releases.forEach(release => {
				const row = document.createElement('tr');
				const statusIcon = release.is_present ? '✅ Present' : `⚠️ Missing`;
				row.innerHTML = `
                    <td>${release.version}</td>
                    <td>${release.date}</td>
                    <td>${statusIcon}</td>
                `;
				releaseHistoryTableBody.appendChild(row);
			});
		} else {
			releaseHistoryTableBody.innerHTML = '<tr><td colspan="3">No releases found.</td></tr>';
		}

		// Populate Active Sites
		const activeSitesTableBody = document.getElementById('activeSitesTableBody');
		activeSitesTableBody.innerHTML = '';
		if (Array.isArray(packageData.active_sites) && packageData.active_sites.length > 0) {
			packageData.active_sites.forEach(site => {
				const row = document.createElement('tr');
				row.innerHTML = `
                    <td><a href="${site.url}" target="_blank">${site.url}</a></td>
                    <td>${site.version}</td>
                    <td>${site.lastReported}</td>
                `;
				activeSitesTableBody.appendChild(row);
			});
		} else {
			activeSitesTableBody.innerHTML = '<tr><td colspan="3">No active sites reported yet.</td></tr>';
		}
	});
});
