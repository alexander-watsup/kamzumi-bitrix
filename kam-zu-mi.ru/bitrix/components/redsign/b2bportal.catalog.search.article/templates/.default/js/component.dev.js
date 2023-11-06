(function(window, B2BPortal)
{
	"use strict";
	
	const ADDTOBASKET_QUANTITY = 1;

	class CatalogSearchArticle
	{

		constructor(el, params)
		{
			this.el = el;
			this.signedParameters = params.signedParameters;

			this.attachTemplate();
		}

		searchAction(query)
		{
			return new Promise((resolve, reject) => {
				BX.ajax.runComponentAction(
					'redsign:b2bportal.catalog.search.article',
					'search',
					{ 
						mode: 'class',
						data: {
							query: query,
							signedParameters: this.signedParameters
						},
					}
				).then(resolve, reject);
			});
		}

		async search(query)
		{
			var foundItems = [];

			try 
			{
				var result = await this.searchAction(query);

				if (result.data)
				{
					foundItems = result.data;
				}
			}
			catch (e)
			{
				window.toastr.error(e.errors[0].message);
			}

			return foundItems;
		}

		addtobasketAction(productId, quantity)
		{
			return new Promise((resolve, reject) => {
				BX.ajax.runComponentAction(
					'redsign:b2bportal.catalog.search.article',
					'addtobasket',
					{ 
						mode: 'class',
						data: {
							productId: productId,
							quantity: quantity
						},
					}
				).then(resolve, reject);
			});
		}

		async addtobasket(item)
		{
			const error = (message) => {
				window.toastr.error(message);
			}

			const success = () => {
				BX.onCustomEvent('updateBasketComponent')
			}
			
			try 
			{
				const result = await this.addtobasketAction(item.id, ADDTOBASKET_QUANTITY);
				const resultData = result.data;

				if (resultData.isSuccess)
				{
					toastr.success(
						(BX.message('RS_B2BPORTAL_CSP_ADDTOBASKET_SUCCESS') || '')
							.replace("#NAME#", item.name)
					);
					success();
				}
				else
				{
					error(resultData.errorMessage);
				}
			}
			catch (e)
			{
				error(((e.errors || [])[0] || {}).message);
			}
		}

		attachTemplate()
		{
			const _c = this;
			const el = this.el;

			this.template = new Vue({
				el: el,

				components: {
					'suggestions-input': B2BPortal.Vue.Components.SuggestionsInput
				},

				template: `
					<suggestions-input 
						:placeholder="messages.placeholder"
						:loadSuggestions="search"
						@select="addtobasket"
					/>
				`,

				data()
				{
					return {
						messages: Object.freeze({
							'placeholder': BX.message('RS_B2BPORTAL_CSP_INPUT_PLACEHOLDER')
						})
					};
				},

				methods: {
					search: _c.search.bind(_c),
					addtobasket: _c.addtobasket.bind(_c)
				}
			});
		}
	}

	window.CatalogSearchArticle = CatalogSearchArticle;
})(window, B2BPortal);