(function(window, B2BPortal)
{
	"use strict";

	class SectionsExtra
	{
		constructor(store, params)
		{
			this.sectionsNode = params.sectionsNode;
			this.pricesNode = params.pricesNode;
			this.changelistNode = params.changelistNode;

			this.store = store;
			
			this.signedParameters  = params.signedParameters;

			this.attachTemplate();
		}

		blockPage(message = '')
		{
			KTApp.blockPage({
				message: '<div id="SE_BlockMessage">' + message + '</div>'
			});
		}

		unblockPage()
		{
			KTApp.unblockPage();
		}

		updateBlockMessage(message = '')
		{
			$("#SE_BlockMessage").text(message);
		}

		showError(message)
		{
			window.toastr.error(
				message
			); 
		}

		showWarning(message)
		{
			window.toastr.warning(
				message
			); 
		}

		prepareActionData()
		{
			const data = {};

			data.signedParameters = this.signedParameters;
			data.changes = [];

			for (let change of this.store.changes)
			{
				data.changes.push({
					SECTION_ID: change.row.ID,
					PRICE_ID: change.priceId,
					VALUE: change.newValue,
					EXTA_ID: change.row.EXTRA[change.priceId].ID
				});
			}

			return data;
		}

		executeSaveAction(data)
		{
			return new Promise((resolve, reject) => {
				BX.ajax.runComponentAction(
					'redsign:b2bportal.sections.extra',
					'save',
					{ 
						mode: 'class',
						data: data,
					}
				).then(resolve, reject)
			});
		}

		async save()
		{
			this.blockPage(
				BX.message('RS_SECTIONS_EXTRA_SAVE_PRICES')
			);

			try
			{
				var lastItemId = 0;
	
				while (this.store.changes.length > 0)
				{
					let data = this.prepareActionData();
					data.nLastItemId = lastItemId;
	
					var result = await this.executeSaveAction(data);;

					if ((result.data.ERRORS || []).length)
					{
						result.data.ERRORS.forEach((errorMessage) => this.showWarning(errorMessage));
					}
	
					if ((result.data.UPDATED || []).length)
					{
						this.store.setExtrasValues(
							this.store.rows,
							B2BPortal.Utils.groupBy(result.data.UPDATED, 'SECTION_ID')
						);
					}
	
					if ((result.data.COMPLETED || []).length)
					{
						this.store.completeChanges(result.data.COMPLETED);
					}
	
					lastItemId = result.data.LAST_ITEM_ID;
				}

				Swal.fire(
					BX.message('RS_SECTIONS_EXTRA_SAVE_SUCCESS'),
					'',
					'success'
				)
			}
			catch (e)
			{
				if (e.errors)
				{
					e.errors.forEach((error) => this.showError(error.message));
				}
				else
				{
					this.showError(BX.message('RS_SECTIONS_EXTRA_ERROR'));
				}
			}
			finally
			{
				this.unblockPage();
			}
		}

		attachSectionsTemplate()
		{
			const store = this.store;

			this.sectionsTemplateInstance = new Vue({
				el: this.sectionsNode,

				components: {
					'sections-extra-table': B2BPortal.Vue.Components.SectionsExtraTable
				},

				template: `
					<sections-extra-table 
						:rows="store.rows"
						:columns="store.columns"
						:priceId="store.priceId"
						:messages="messages"
						@setExtraValue="addChange"
					/>`,

				computed: {
					messages()
					{
						return Object.freeze({
							'CONFIRM_SET_EXTRA': BX.message('RS_SECTIONS_EXTRA_CONFIRM_SET_EXTRA'),
							'CONFIRM_SET_EXTRA_CATALOG': BX.message('RS_SECTIONS_EXTRA_CONFIRM_SET_EXTRA'),
							'CATALOG': BX.message('RS_SECTIONS_EXTRA_CATALOG')
						});
					},

				},

				data()
				{
					return {
						store
					}
				},

				methods: {

					addChange(data)
					{
						this.store.addChange(data);
					}

				}
			});
		}

		attachPricesTemplate()
		{
			const store = this.store;

			this.pricesTemplateInstance = new Vue({
				el: this.pricesNode,
				
				components: {
					'dropdown-select': B2BPortal.Vue.Components.Select
				},

				template: '<dropdown-select :variants="variants" :selected="store.priceId" @select="onSelect" />',

				computed: {
					variants() 
					{
						let variants = {};

						for (var i in this.store.prices)
						{
							if (this.store.prices.hasOwnProperty(i))
							{
								variants[i] = {
									id: i,
									text: this.store.prices[i]['NAME']
								};
							}
						}

						return variants;
					},
				},

				data()
				{
					return {
						store
					}
				},

				methods: {
					onSelect(selectedVariant) 
					{
						this.store.selectPrice(selectedVariant.id);
					}
				}
			});
		}

		attachChangelistTemplate()
		{
			const store = this.store;
			const component = this;

			this.changelistTemplateInstance = new Vue({
				el: this.changelistNode,

				components: {
					'sections-extra-changelist': B2BPortal.Vue.Components.SectionsExtraChangelist
				},

				template: `
					<sections-extra-changelist 
						:changes="store.changes"
						:messages="messages"
						@reset="store.resetChanges()"
						@save="save"
					/>`,

				computed: {
					messages()
					{
						return Object.freeze({
							'RESET': BX.message('RS_SECTIONS_EXTRA_RESET_CHANGES'),
							'SAVE': BX.message('RS_SECTIONS_EXTRA_SAVE_CHANGES'),
							'EMPTY': BX.message('RS_SECTIONS_EXTRA_CHANGES_EMPTY'),
							'ARE_YOU_SURE': BX.message('RS_SECTIONS_EXTRA_ARE_YOU_SURE')
						});
					},
				},

				methods: {
					save()
					{
						component.save();
					}
				},

				data()
				{
					return {
						store
					}
				},
			});
		}

		attachTemplate() 
		{
			this.attachPricesTemplate();
			this.attachSectionsTemplate()
			this.attachChangelistTemplate()
		}
	}
	
	window.SectionsExtra = SectionsExtra;
})(window, B2BPortal);