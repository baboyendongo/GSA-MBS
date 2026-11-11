const setupSearch = (inputSelector, rowSelector, emptySelector, countSelector) => {
	const searchInput = document.querySelector(inputSelector);
	if (!searchInput || searchInput.form?.method.toLowerCase() === 'get') return;

	const rows = [...document.querySelectorAll(rowSelector)];
	const emptyState = document.querySelector(emptySelector);
	const visibleCount = document.querySelector(countSelector);

	searchInput?.addEventListener('input', () => {
		const query = searchInput.value.trim().toLocaleLowerCase('fr');
		let visibleRows = 0;

		rows.forEach((row) => {
			const matches = row.dataset.search.includes(query);
			row.hidden = !matches;
			visibleRows += Number(matches);
		});

		if (emptyState) emptyState.hidden = visibleRows !== 0;
		if (visibleCount) visibleCount.textContent = visibleRows.toString();
	});
};

setupSearch('#instancel-search', '[data-instancel-row]', '#instancel-empty-search', '#instancel-visible-count');
setupSearch('#instance-search', '[data-instance-row]', '#instance-empty-search', '#instance-visible-count');
setupSearch('#project-search', '[data-project-row]', '#project-empty-search', '#project-visible-count');

const setupDialog = (dialogSelector, openSelector, closeSelectors) => {
	const dialog = document.querySelector(dialogSelector);
	if (!dialog) return;

	document.querySelector(openSelector)?.addEventListener('click', () => dialog.showModal());
	closeSelectors.forEach((selector) => document.querySelector(selector)?.addEventListener('click', () => dialog.close()));
	dialog.addEventListener('click', (event) => {
		if (event.target === dialog) dialog.close();
	});
	if (dialog.dataset.open === 'true') dialog.showModal();
};

setupDialog('#instancel-dialog', '#open-instancel-dialog', ['#close-instancel-dialog', '#cancel-instancel-dialog']);
setupDialog('#instance-dialog', '#open-instance-dialog', ['#close-instance-dialog', '#cancel-instance-dialog']);
setupDialog('#project-dialog', '#open-project-dialog', ['#close-project-dialog', '#cancel-project-dialog']);
setupDialog('#admin-create-state-dialog', '#open-admin-create-state', ['#close-admin-create-state', '#cancel-admin-create-state']);
setupDialog('#admin-create-user-dialog', '#open-admin-create-user', ['#close-admin-create-user', '#cancel-admin-create-user']);

const setupRecordDialogs = (config) => {
	const editDialog = document.querySelector(config.editDialog);
	const editForm = document.querySelector(config.editForm);
	const deleteDialog = document.querySelector(config.deleteDialog);
	const deleteForm = document.querySelector(config.deleteForm);
	const deleteName = document.querySelector(config.deleteName);

	if (editDialog && editForm) {
		const openEditDialog = (button) => {
			editForm.action = button.dataset.updateUrl;
			Object.entries(config.fields).forEach(([name, dataKey]) => {
				const field = editForm.elements.namedItem(name);
				if (!field) return;
				if (field.type === 'checkbox') {
					field.checked = button.dataset[dataKey] === '1';
				} else {
					field.value = button.dataset[dataKey] ?? '';
				}
			});
			editDialog.showModal();
		};

		document.querySelectorAll(config.editButtons).forEach((button) => {
			button.addEventListener('click', () => openEditDialog(button));
		});

		config.editCloseButtons.forEach((selector) => {
			document.querySelector(selector)?.addEventListener('click', () => editDialog.close());
		});

		editDialog.addEventListener('click', (event) => {
			if (event.target === editDialog) editDialog.close();
		});

		if (editDialog.dataset.open === 'true') {
			const button = [...document.querySelectorAll(config.editButtons)]
				.find((item) => item.dataset.recordId === editDialog.dataset.recordId);

			if (button) {
				openEditDialog(button);
				try {
					const oldValues = JSON.parse(editDialog.dataset.oldValues || '{}');
					Object.keys(config.fields).forEach((name) => {
						const field = editForm.elements.namedItem(name);
						if (!field || !Object.hasOwn(oldValues, name)) return;
						if (field.type === 'checkbox') {
							field.checked = ['1', 1, true, 'on'].includes(oldValues[name]);
						} else {
							field.value = oldValues[name] ?? '';
						}
					});
				} catch {
					editDialog.showModal();
				}
			}
		}
	}

	if (deleteDialog && deleteForm) {
		document.querySelectorAll(config.deleteButtons).forEach((button) => {
			button.addEventListener('click', () => {
				deleteForm.action = button.dataset.deleteUrl;
				if (deleteName) deleteName.textContent = button.dataset.recordName ?? '';
				deleteDialog.showModal();
			});
		});

		config.deleteCloseButtons.forEach((selector) => {
			document.querySelector(selector)?.addEventListener('click', () => deleteDialog.close());
		});

		deleteDialog.addEventListener('click', (event) => {
			if (event.target === deleteDialog) deleteDialog.close();
		});
	}
};

setupRecordDialogs({
	editDialog: '#admin-state-edit-dialog',
	editForm: '#admin-state-edit-form',
	editButtons: '[data-admin-state-edit]',
	editCloseButtons: ['#close-admin-state-edit', '#cancel-admin-state-edit'],
	fields: { label: 'stateLabel' },
	deleteDialog: '#admin-state-delete-dialog',
	deleteForm: '#admin-state-delete-form',
	deleteButtons: '[data-admin-state-delete]',
	deleteCloseButtons: ['#close-admin-state-delete', '#cancel-admin-state-delete'],
	deleteName: '#admin-state-delete-name',
});

setupRecordDialogs({
	editDialog: '#admin-user-edit-dialog',
	editForm: '#admin-user-edit-form',
	editButtons: '[data-admin-user-edit]',
	editCloseButtons: ['#close-admin-user-edit', '#cancel-admin-user-edit'],
	fields: { name: 'name', email: 'email', is_admin: 'isAdmin' },
	deleteDialog: '#admin-user-delete-dialog',
	deleteForm: '#admin-user-delete-form',
	deleteButtons: '[data-admin-user-delete]',
	deleteCloseButtons: ['#close-admin-user-delete', '#cancel-admin-user-delete'],
	deleteName: '#admin-user-delete-name',
});

setupRecordDialogs({
	editDialog: '#instancel-edit-dialog',
	editForm: '#instancel-edit-form',
	editButtons: '[data-instancel-edit]',
	editCloseButtons: ['#close-instancel-edit-dialog', '#cancel-instancel-edit-dialog'],
	fields: { date: 'date', evenement: 'evenement', clients: 'clients', ligne: 'ligne', phase: 'phase', responsable: 'responsable', delai: 'delai', date_cloture: 'dateCloture', observation: 'observation' },
	deleteDialog: '#instancel-delete-dialog',
	deleteForm: '#instancel-delete-form',
	deleteButtons: '[data-instancel-delete]',
	deleteCloseButtons: ['#close-instancel-delete-dialog', '#cancel-instancel-delete-dialog'],
	deleteName: '#instancel-delete-name',
});

setupRecordDialogs({
	editDialog: '#instance-edit-dialog',
	editForm: '#instance-edit-form',
	editButtons: '[data-instance-edit]',
	editCloseButtons: ['#close-instance-edit-dialog', '#cancel-instance-edit-dialog'],
	fields: { date: 'date', instance: 'instance', client: 'client', ligne: 'ligne', responsable: 'responsable', etat: 'etat', delai: 'delai', date_cloture: 'dateCloture', observation: 'observation' },
	deleteDialog: '#instance-delete-dialog',
	deleteForm: '#instance-delete-form',
	deleteButtons: '[data-instance-delete]',
	deleteCloseButtons: ['#close-instance-delete-dialog', '#cancel-instance-delete-dialog'],
	deleteName: '#instance-delete-name',
});

setupRecordDialogs({
	editDialog: '#project-edit-dialog',
	editForm: '#project-edit-form',
	editButtons: '[data-project-edit]',
	editCloseButtons: ['#close-project-edit-dialog', '#cancel-project-edit-dialog'],
	fields: { date: 'date', project: 'project', client: 'client', Action: 'action', responsable: 'responsable', etat: 'etat', delai: 'delai', date_cloture: 'dateCloture', observation: 'observation' },
	deleteDialog: '#project-delete-dialog',
	deleteForm: '#project-delete-form',
	deleteButtons: '[data-project-delete]',
	deleteCloseButtons: ['#close-project-delete-dialog', '#cancel-project-delete-dialog'],
	deleteName: '#project-delete-name',
});

const projectDetailDialog = document.querySelector('#project-detail-dialog');
if (projectDetailDialog) {
	const projectName = document.querySelector('#project-detail-name');
	const projectDeadline = document.querySelector('#project-detail-deadline');
	const projectCloseDate = document.querySelector('#project-detail-close-date');
	const projectObservation = document.querySelector('#project-detail-observation');

	document.querySelectorAll('[data-project-detail]').forEach((button) => {
		button.addEventListener('click', () => {
			projectName.textContent = button.dataset.projectTitle || 'Projet B2B';
			projectDeadline.textContent = button.dataset.projectDeadline || '—';
			projectCloseDate.textContent = button.dataset.projectCloseDate || '—';
			projectObservation.textContent = button.dataset.projectObservation || '—';
			projectDetailDialog.showModal();
		});
	});

	document.querySelector('#close-project-detail-dialog')?.addEventListener('click', () => projectDetailDialog.close());
	projectDetailDialog.addEventListener('click', (event) => {
		if (event.target === projectDetailDialog) projectDetailDialog.close();
	});
}

const instancelDetailDialog = document.querySelector('#instancel-detail-dialog');
if (instancelDetailDialog) {
	const instancelName = document.querySelector('#instancel-detail-name');
	const instancelCloseDate = document.querySelector('#instancel-detail-close-date');
	const instancelObservation = document.querySelector('#instancel-detail-observation');

	document.querySelectorAll('[data-instancel-detail]').forEach((button) => {
		button.addEventListener('click', () => {
			instancelName.textContent = button.dataset.instancelTitle || 'Instancel';
			instancelCloseDate.textContent = button.dataset.instancelCloseDate || '—';
			instancelObservation.textContent = button.dataset.instancelObservation || '—';
			instancelDetailDialog.showModal();
		});
	});

	document.querySelector('#close-instancel-detail-dialog')?.addEventListener('click', () => instancelDetailDialog.close());
	instancelDetailDialog.addEventListener('click', (event) => {
		if (event.target === instancelDetailDialog) instancelDetailDialog.close();
	});
}

const instanceDetailDialog = document.querySelector('#instance-detail-dialog');
if (instanceDetailDialog) {
	const instanceName = document.querySelector('#instance-detail-name');
	const instanceDeadline = document.querySelector('#instance-detail-deadline');
	const instanceCloseDate = document.querySelector('#instance-detail-close-date');
	const instanceObservation = document.querySelector('#instance-detail-observation');

	document.querySelectorAll('[data-instance-detail]').forEach((button) => {
		button.addEventListener('click', () => {
			instanceName.textContent = button.dataset.instanceTitle || 'Instance';
			instanceDeadline.textContent = button.dataset.instanceDeadline || '—';
			instanceCloseDate.textContent = button.dataset.instanceCloseDate || '—';
			instanceObservation.textContent = button.dataset.instanceObservation || '—';
			instanceDetailDialog.showModal();
		});
	});

	document.querySelector('#close-instance-detail-dialog')?.addEventListener('click', () => instanceDetailDialog.close());
	instanceDetailDialog.addEventListener('click', (event) => {
		if (event.target === instanceDetailDialog) instanceDetailDialog.close();
	});
}
