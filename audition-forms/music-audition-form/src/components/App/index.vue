<template>
    <div class="app">
        <div class="row">
            <text-input v-for="(input, i) of fields.studentInfo" :valueParent="`studentInfo`" :key="i" :label="input.label" :name="input.name" :colWidth="input.colWidth" :type="input.type !== undefined ? input.type : null" :val="input.val !== undefined ? input.val : null" :maxLength="input.maxLength !== undefined ? input.maxLength : null" />
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>