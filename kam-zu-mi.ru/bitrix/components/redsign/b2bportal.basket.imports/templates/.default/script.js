(function (window, document) {

	class CatalogImport
	{
		constructor(input, saveBtn)
		{
			this.input = input;
			this.saveBtn = saveBtn;

			if (this.saveBtn)
			{
				this.saveBtn.addEventListener('click', (e) => {
					// const data = this.input.value.split('\n');
					// this.send(data);
				});
			}

			this.attachInputTemplate();
		}

		attachInputTemplate()
		{
			this.template = new Vue({
				components: {
					'catalog-import': B2BPortal.Vue.Components.BasketCatalogImport,
				},

				template: `<catalog-import / >`
			});

			console.log(this.template)
		}
	}

	window.CatalogImport = CatalogImport;

}(window, document));

