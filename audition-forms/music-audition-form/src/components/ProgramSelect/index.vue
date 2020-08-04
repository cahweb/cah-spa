<template>
    <div class="form-section mb-4" id="program-select">
        <h2 class="heading-underline">Program Information</h2>
        <p>Are you applying to an Undergraduate or Graduate program?</p>
        <button-group :name="'level'" :buttonList="levels" :inline="true" :emitOn="`levelChange`" />
        <div v-if="level !== ''" id="program-select" class="row">
            <p>Which program are you applying for?</p>
            <button-group v-if="level === 'undergrad'" :buttonList="programs.undergrad" name="program" :inline="true" emitOn="programChange" />
            <button-group v-else-if="level === 'grad'" :buttonList="programs.grad" name="program" :inline="true" emitOn="programChange" />
        </div>
        <div v-if="showTracks" id="track-select" class="row">
            <p>Which track?</p>
            <button-group :buttonList="programs.tracks" name="track" :inline="true" emitOn="trackChange" />
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>