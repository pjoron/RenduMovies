<script>
export default {
	name: "Modal",
	methods: {
		close() {
			this.$emit("close");
		},

		handleDelete() {
			this.$emit("handleDelete");
		},
	},
};
</script>

<template>
	<div class="modal-backdrop">
		<div class="modal">
			<section class="modal-body">
				<slot name="body">Are you sure?</slot>
			</section>

			<footer class="modal-footer">
				<button type="button" class="btn-red" @click="handleDelete">Delete</button>
				<button type="button" class="btn-green" @click="close">Cancel</button>
			</footer>
		</div>
	</div>
</template>

<style>
.modal-backdrop {
	position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background-color: rgba(0, 0, 0, 0.3);
	display: flex;
	justify-content: center;
	align-items: center;
}

.modal {
	background: #ffffff;
	box-shadow: 2px 2px 20px 1px;
	overflow-x: auto;
	display: flex;
	flex-direction: column;
	width: 500px;
}

.modal-header,
.modal-footer {
	padding: 15px;
	display: flex;
}

.modal-header {
	position: relative;
	border-bottom: 1px solid #eeeeee;
	color: #4aae9b;
	justify-content: space-between;
	align-items: center;
}

.modal-footer {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	gap: 10px;
}

.modal-body {
	position: relative;
	padding: 20px;
	font-size: 16px;
	align-self: flex-start;
}

.btn-green {
	color: white;
	background: #4aae9b;
	border: 1px solid #4aae9b;
	border-radius: 2px;
	font-size: 16px;
	padding: 10px 20px;
	transition: filter 0.3s;
}

.btn-red {
	color: white;
	background: #e74c3c;
	border: 1px solid #e74c3c;
	border-radius: 2px;
	font-size: 16px;
	padding: 10px 20px;
	transition: filter 0.3s;
}

.btn-green:hover,
.btn-red:hover {
	cursor: pointer;
	filter: brightness(90%);
}
</style>
