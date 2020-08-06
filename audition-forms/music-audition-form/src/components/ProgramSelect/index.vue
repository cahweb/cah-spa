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
        <div v-if="showReqs" class="row mb-3 w-75 mx-0">
            <div class="col-12 px-0">
                <h2 class="heading-underline">Program Requirements for {{ programName }}</h2>
                <div v-html="programReqs" id="program-reqs"></div>
            </div>
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>