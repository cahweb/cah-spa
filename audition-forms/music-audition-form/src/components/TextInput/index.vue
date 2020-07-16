<template>
    <div class="form-group" :class="colWidth !== null ? `col-md-${colWidth}` : ''">
        <label>{{ label }}:</label>
        <input :type="type !== null ? type : 'text'" class="form-control" :name="name" v-model="value" @change="fieldChange" :emitOn="textInputChange" :maxlength="maxLength">
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>