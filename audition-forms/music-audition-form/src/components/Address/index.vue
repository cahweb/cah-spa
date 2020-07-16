<template>
    <div class="address" :class="`col-${colWidth !== null ? `md-${colWidth}` : '12'}`">
        <text-input v-for="(field, i) in fields" :key="i" :colWidth="field.colWidth" :type="field.type !== undefined ? field.type : null" :label="field.label" :name="field.name" :maxLength="field.maxLength" :emitOn="'addressChange'" />
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>