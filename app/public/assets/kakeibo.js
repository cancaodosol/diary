class kakeiboRowAdder {
    constructor(rowsBodyId, templateId, addButtonId) {
        this.rowsBody = document.getElementById(rowsBodyId);
        this.template = document.getElementById(templateId);
        this.addButton = document.getElementById(addButtonId);
    }

    init() {
        if (!this.rowsBody || !this.template || !this.addButton) return;
        this.addButton.onclick = () => this.addRow();
    }

    addRow() {
        const row = this.template.content.cloneNode(true);
        this.rowsBody.appendChild(row);
    }
}

function initKakeiboRows() {
    const adder = new kakeiboRowAdder('kakeibo-rows', 'kakeibo-row-template', 'btn-add-kakeibo-row');
    adder.init();
}
