<template>
    <div class="form-section">
        <h2 class="heading-underline">Audition Information</h2>
        <p>UCF Clas Standing upon start of program:</p>
        <div class="row w-75 button-container">
            <button-group :buttonList="buttonList" 
                name="year" 
                :inline="true" 
                emitOn="yearChange"
            />
        </div>
        <div class="row w-75">
            <text-input v-for="(field, i) of fieldList" :key="i" 
                :name="field.name" 
                :type="field.type" 
                :label="field.label" 
                :max-length="field.maxLength !== undefined ? field.maxLength : null" 
                emitOn="textInputChange"
                :isRequired="field.required"
            />
        </div>
        <h5 class="subsection-title text-muted">Instrument Details</h5>
        <div class="row w-75">
            <div class="form-group col-md-6">
                <label>Primary Instrument or Voice Type:</label>
                <select class="form-control" 
                    name="instrument" 
                    v-model="instrument" 
                    @change="instrumentChange"
                    required
                >
                    <option value=""> -- Please Select -- </option>
                    <option v-for="(option, i) of instrumentList[discipline]" 
                        :key="i" 
                        :value="option.value"
                    >
                        {{ option.name }}
                    </option>
                </select>
            </div>
            <div class="col-12">
                <div class="row">
                    <text-input name="instrumentYears" 
                        label="Years of Private Study" 
                        type="number" 
                        emitOn="textInputChange"
                        :isRequired="true"
                    />
                    <text-input name="instrumentTeacher"
                        label="Private Teacher's Name"
                        emitOn="textInputChange"
                        :isRequired="true"
                    />
                </div>
            </div>
        </div>
        <h5 class="subsection-title text-muted">Audition Date</h5>
        <div class="row w-75">
            <div class="form-group col-md-6">
                <label>Please select your preferred audition date:</label>
                <select class="form-control" 
                    name="date" 
                    v-model="date" 
                    @change="dateChange"
                    required
                >
                    <option value=""> -- Please Select -- </option>
                    <template v-for="(option, i) of dateList">
                        <option v-if="!isBlackedOut(option.value)" 
                            :key="i" 
                            :value="option.value"
                        >
                            {{ option.text }}
                        </option>
                    </template>
                </select>
                <p class="form-text text-muted"><small>Auditions after February 13 will not be considered for scholarships, but will still be considered for admission.</small></p>
            </div>
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>