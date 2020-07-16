<template>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" :style="`width: ${value}%;`" :aria-valuenow="value" aria-valuemin="0" aria-valuemax="100" />
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>