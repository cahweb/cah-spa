<template>
    <div class="app">
        <div v-if="!isSubmitted" class="form-container">
            <form id="theatre-audition-form" method="post" enctype="multipart/form-data" ref="form">
                <p>Are you auditioning for an Undergraduate or Graduate program?</p>
                <div class="radio-group mb-3">
                    <template v-for="(button, i) in buttons.level">
                        <div :key="i" class="form-check form-check-inline mx-2">
                            <label class="form-check-label btn btn-primary btn-sm">
                                <input type="radio" :id="`level-${i}`" name="level" class="form-check-input" v-model="values.level" :value="button.value" @click="updateActive('level')">
                                {{ button.label }}
                            </label>
                        </div>
                    </template>
                </div>
                <div v-if="values.level !== ''" class="mb-5">
                    <p>Which program were you interested in?</p>
                    <div class="radio-group mb-3">
                        <template v-for="(button, i) in (values.level == 'undergrad' ? buttons.programs.undergrad : buttons.programs.grad)">
                            <div :key="i" class="form-check form-check-inline mx-2">
                                <label class="form-check-label btn btn-primary btn-sm">
                                    <input type="radio" :id="`program-${i}`" name="program" class="form-check-input" v-model="values.program" :value="button" @click="updateActive('program')">
                                    {{ button.label }}
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="showReqs" class="mb-5">
                    <h2 class="heading-underline">Program Requirements for {{ values.program.label }}</h2>
                    <div v-html="programReqs"></div>
                </div>
                <div v-if="isBfaActing" class="mb-3">
                    <p class="mt-4">Did you want to schedule a live audition or submit a video audition?</p>
                    <div class="radio-group">
                        <template v-for="(button, i) in buttons.bfaActingChoice">
                            <div :key="i" class="form-check form-check-inline mx-2">
                                <label class="form-check-label btn btn-primary btn-sm">
                                    <input type="radio" :id="`bfa-acting-choice-${i}`" name="actingChoice" class="form-check-input" v-model="values.actingChoice" :value="button.value" @click="updateActive('actingChoice')">
                                    {{ button.label }}
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="showForm" class="mb-3">
                    <h4>Student Information</h4>
                    <div class="row w-75">
                        <template v-for="(input, i) in inputs.studentInfo">
                            <div :key="i" class="form-group col-md-6">
                                <label>{{ input.label }}:</label>
                                <input :type="input.type !== undefined ? input.type : 'text'" :name="input.name" :maxlength="input.maxlength !== undefined ? input.maxlength : null" v-model="values.studentInfo[input.name]" class="form-control" :required="input.required">
                            </div>
                        </template>
                    </div>
                    <div class="row w-75 mb-3">
                        <template v-for="(input, i) in inputs.address">
                            <div :key="i" class="form-group" :class="`col-md-${input.colWidth !== undefined ? input.colWidth : 6}`">
                                <label>{{ input.label }}:</label>
                                <input :type="input.type !== undefined ? input.type : 'text'" :name="input.name" :maxlength="input.maxlength !== undefined ? input.maxlength : null" v-model="values.studentInfo.address[input.name]" class="form-control">
                            </div>
                        </template>
                    </div>
                    <h4>Audition Information</h4>
                    <div class="row w-75 mb-3">
                        <template v-for="(ordinal, i) in ['First', 'Second']">
                            <div :key="i" class="form-group col-md-7" :class="{'has-danger': sameDate}">
                                <label>{{ ordinal }} Choice Date:</label>
                                <select :name="`audition${i + 1}`" v-model="values.auditionDates[ordinal.toLowerCase()]" class="form-control" :class="{'form-control-danger': sameDate}">
                                    <option value="">-- Please Select --</option>
                                    <option v-for="(date, j) in dateList" :key="j" :value="date.value">{{ date.text }}</option>
                                </select>
                                <div v-if="ordinal === 'Second' && sameDate" class="form-control-feedback">Can't select the same date.</div>
                            </div>
                        </template>
                    </div>
                    <h5>Additional Documents</h5>
                    <div class="row w-75">
                        <p class="col-12">R&eacute;sum&eacute; is required.</p>
                        <div class="col-md-7">
                            <file-input :name="`resume`" :file="values.files.resume" :acceptedFileTypes="acceptedFileTypes" :index="-1" :required="true" @fileChange="payload => {fileChange(payload)}" @removeFile="payload => {removeFile(payload)}" />
                        </div>
                        <div v-if="values.files.extra.length > 0" class="col-md-7">
                            <file-input v-for="(file, i) in extraFileList" :key="i" :index="i" :required="false" :name="`file-extra-${i}`" :file="file" :acceptedFileTypes="acceptedFileTypes" @fileChange="payload => {fileChange(payload)}" @removeFile="payload => {removeFile(payload)}" />
                        </div>
                    </div>
                    <div class="row w-75">
                        <div class="col-md-7">
                            <button type="button" class="btn btn-complementary btn-sm mt-2 rounded float-right" @click="newFile">&plus;</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary" @click="submitForm">Submit</button>
                </div>
                <div v-if="showAcceptd" class="mb-3">
                    <p v-html="acceptdMessage" />
                </div>
            </form>
        </div>
        <div v-else class="status-container w-100 rounded" style="min-height: 100px;">
            <p class="text-center" id="status-msg">{{ statusMessage }}</p>
            <progress-bar v-if="isProcessing" :value="uploadPercentage" />
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>
