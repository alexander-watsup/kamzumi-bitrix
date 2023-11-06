<template>
		<div class="modal" :class="modalClasses" v-show="isShow" ref="modal" @click.self="hide()">
			<div class="modal-dialog" @keyup.esc="close()">
				<div class="modal-content">
					
					<div class="modal-header">
						<button type="button" class="close" @click="hide">
							<i class="fa fa-close"></i>
						</button>
						<h4 class="modal-title">
							<slot name="title"> {{ title }} </slot>
						</h4>
					</div>

					<div class="modal-body">
						<slot></slot>
					</div>

					<div class="modal-footer">
						<slot name="footer">
							<button type="button" class="btn btn-primary" @click="$emit('save');"> {{ saveTitle }} </button>
						</slot>
					</div>
				</div>
			</div>
		</div>
</template>

<script>
export default {

	props: {
		title: String,
		saveTitle: String
	},

	data()
	{
		return {
			isShow: false
		};
	},

	computed: {

		modalClasses()
		{
			return {
				'fade': !this.isShow,
				'show': this.isShow
			};
		}

	},

	mounted()
	{
		window.addEventListener('keyup', e => {

			if(this.isShow && (e.key=='Escape' || e.key=='Esc' || e.keyCode==27)) 
			{
				this.hide();
			}

		});
	},

	methods: {

		show()
		{
			if (this.isShow)
			{
				return;
			}

			this.isShow = true;
			this.$nextTick(() => {
				this.$refs.modal.focus()
			});
		},

		hide()
		{
			if (!this.isShow)
			{
				return;
			}

			this.isShow = false;
		}

	}

}
</script>

<style lang="scss" scoped>
.modal {
	background-color: rgba(0, 0, 0, .25);
}

.modal-dialog {
	top: 50%;
	transform: translateY(-100%);
	margin: 0 auto;
}
</style>