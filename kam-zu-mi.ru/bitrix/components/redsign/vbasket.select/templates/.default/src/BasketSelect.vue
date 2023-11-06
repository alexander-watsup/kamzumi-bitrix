<template>
	<div class="basket-select">
		
		<div 
			class="basket-select__item" 
			v-for="basket in data" 
			:key="basket.ID" 
			:class="{'basket-select__item--active': basket.SELECTED}"
			:style=" basket.COLOR ? 'color: ' + basket.COLOR : ''"
		>

			<button 
				type="button" 
				class="basket-select__button"
				@click="!basket.SELECTED && selectAction(basket.CODE)"
			>
				<span class="basket-select__text">{{ basket['~NAME'] }} ({{ basket.CNT }})</span>
			</button>
			<button v-if="basket.SELECTED" type="button" class="basket-select__icon" @click="editBasket(basket)">
				<i class="basket-select__text fa fa-pencil" aria-hidden="true"></i>
			</button>
			<button v-if="basket.SELECTED" type="button" class="basket-select__icon" @click="removeAction(basket.CODE)">
				<i class="fa fa-times basket-select__text" aria-hidden="true"></i>
			</button>
		</div>
	
		<button class="btn basket-select__edit-button" @click="createBasket()"><i class="fa fa-plus" aria-hidden="true" ></i></button>

		<basket-select-modal :title="modalTitle" :saveTitle="messages.RS_VBASKET_SELECT_SAVE" ref="form" @save="saveAction"> 
			<p> {{ modalDescription }} </p>
			<div style="display: flex; align-items:flex-start;">
				<div class="form-group" :class="{'has-error': hasInputError}" style="margin: 0; flex: 1;">
					<input :placeholder="messages.RS_VBASKET_SELECT_BASKET_NAME_PLACEHOLDER" type="text" class="form-control" v-model="formData.name" minlength="3" maxlength="20" required ref="input" @input="handleInput">
					 <span id="helpBlock2" v-if="hasInputError" class="help-block">{{ inputErrorMessage }}</span>
				</div>
				<swatches 
					ref="swatches"
					style="margin-left: 10px; line-height: 1;"
					v-model="formData.color" 
					popover-to="left"
					:colors="colors"
					swatch-size="32" 
					:trigger-style="{width: '36px', height: '36px', borderRadius: '4px', border: 0}"
				></swatches>
			</div>
		</basket-select-modal>

	</div>
</template>

<script>
import BasketSelectModal from './BasketSelectModal.vue';
import Swatches from 'vue-swatches';
import "vue-swatches/dist/vue-swatches.min.css";

export default {

	components: { BasketSelectModal, Swatches },
	
	props: {
		data: Array,
		colors: Array,
		defaultColor: String
	},

	data()
	{
		return {
			hasInputError: false,
			inputErrorMessage: '',
			formData: {
				code: '',
				originalName: '',
				name: '',
				color: this.defaultColor
			},
		}
	},

	computed: {

		messages()
		{
			return BX.Vue.getFilteredPhrases('RS_VBASKET_');
		},

		modalTitle()
		{
			if (this.formData.code.trim() == '')
			{
				return this.messages.RS_VBASKET_SELECT_NEW_BASKET;
			}
			else
			{
				return this.messages.RS_VBASKET_SELECT_EDIT_BASKET.replace('#NAME#', this.formData.originalName);
			}
		},

		modalDescription()
		{
			if (this.formData.code.trim() == '')
			{
				return this.messages.RS_VBASKET_SELECT_NEW_BASKET_DESCR;
			}
			else
			{
				return this.messages.RS_VBASKET_SELECT_EDIT_BASKET_DESCR;
			}
		}

	},

	methods: {

		resetFormData()
		{
			this.formData = {
				code: '',
				name: '',
				originalName: '',
				color: this.defaultColor
			};
		},

		createBasket()
		{
			this.resetFormData();
			this.$refs.form.show();
		},

		editBasket(basket)
		{
			this.formData = {
				code: basket.CODE,
				name: basket['~NAME'],
				originalName: basket['~NAME'],
				color: basket.COLOR
			};

			this.$refs.form.show();
		},

		handleInput()
		{
			if (this.hasInputError)
			{
				if (this.$refs.input.checkValidity())
				{
					this.hasInputError = false;
					this.inputErrorMessage = '';
				}
				else
				{
					this.inputErrorMessage = this.$refs.input.validationMessage;
				}
			}
		},

		runAction(actionName, data)
		{
			const action = 'redsign:vbasket.api.userbasket.' + actionName;
			return new Promise((resolve, reject) => {
				BX.ajax.runAction(action, { data }).then(result => result.data ? resolve() : reject(result), reject);
			});
		},

		async saveAction()
		{
			if (this.$refs.input.checkValidity())
			{
				try
				{
					this.$refs.form.hide();
					const result = await this.runAction('save', { 
						code: this.formData.code,
						name: this.formData.name,
						color: this.formData.color
					});
					
					BX.reload();
				}
				catch(e)
				{
					BX.UI.Notification.Center.notify({
						content: this.messages.RS_VBASKET_SELECT_SAVE_ERROR
					});
	
					console.error(e);
				}
			}
			else
			{
				this.hasInputError = true;
				this.inputErrorMessage = this.$refs.input.validationMessage;
			}
		},

		async removeAction(code)
		{
			try
			{
				if (confirm(this.messages.RS_VBASKET_SELECT_ARE_YOUR_SURE))
				{
					await this.runAction('remove', { code });
					BX.reload();
				}
			}
			catch(e)
			{
				BX.UI.Notification.Center.notify({
					content: this.messages.RS_VBASKET_SELECT_REMOVE_ERROR
				});

				console.error(e);
			}
		},

		async selectAction(code)
		{
			await this.runAction('select', { code });
			BX.reload();
		}

	}

}
</script>

<style lang="scss">
.vue-swatches__wrapper {
	box-sizing: content-box;
}
</style>

<style lang="scss" scoped>
.basket-select {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	margin-bottom: 5px;

	&__item {
		color: #1485CE;
		padding: 10px 0px;
		margin: 0 5px 10px;
		border-bottom: 1px solid #e2dfdf;
		display: flex;
		align-items: center;
	}

	&__item--active {
		border-bottom-color: currentColor;
		font-weight: bold;
	}

	&__button,
	&__icon {
		border: 0;
		outline: 0;
		background: transparent;
		color: inherit;
	}

	&__text {
		color: #333;
	}

	&__icon:hover {
		color: currentColor;
	}

	&__icon > &__text {
		color: #e2dfdf;
	}

	&__icon:hover > &__text {
		color: inherit;
	}

	&__edit-button {
		margin-left: 10px;

		color: #333;
		background-color: #fff;
		border-color: #ccc;

		&:hover {
			color: #333;
			background-color: #e6e6e6;
			border-color: #adadad;
		}
	}
}
</style>