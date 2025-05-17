<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import DateRangePicker from "@/Components/DateRangePicker.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";

const page = usePage();
const currentHour = ref(new Date().getHours());
const greeting = computed(() => {
    if (currentHour.value >= 5 && currentHour.value < 12) {
        return "Bom dia";
    } else if (currentHour.value >= 12 && currentHour.value < 18) {
        return "Boa tarde";
    } else {
        return "Boa noite";
    }
});

const currentTime = ref("");
let timeInterval = null;

const updateTime = () => {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    const seconds = String(now.getSeconds()).padStart(2, "0");
    currentTime.value = `${hours}:${minutes}:${seconds}`;
    currentHour.value = now.getHours();
};

const formattedDate = computed(() => {
    const now = new Date();
    const weekdays = [
        "Domingo",
        "Segunda-feira",
        "Terça-feira",
        "Quarta-feira",
        "Quinta-feira",
        "Sexta-feira",
        "Sábado",
    ];
    const months = [
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro",
    ];

    const weekday = weekdays[now.getDay()];
    const day = now.getDate();
    const month = months[now.getMonth()];
    const year = now.getFullYear();

    return `${weekday}, ${day} de ${month} de ${year}`;
});

onMounted(() => {
    updateTime();
    timeInterval = setInterval(updateTime, 1000);
});

onBeforeUnmount(() => {
    if (timeInterval) {
        clearInterval(timeInterval);
    }
});

const handleDateChange = ({ startDate: newStartDate, endDate: newEndDate }) => {
    console.log("Start Date:", newStartDate);
    console.log("End Date:", newEndDate);
};
</script>

<template>
    <Head title="Home" />

    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>{{ greeting }}, {{ $page.props.auth.user.name }}!</h4>
                <span class="text-muted text-lowercase">{{
                    formattedDate
                }}</span>
            </div>
            <div class="ml-auto text-right">
                <h2>{{ currentTime }}</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Teste
            </div>
            <div class="card-body">
                <DateRangePicker
                    placeholder="Período:"
                    @apply="handleDateChange"
                />
            </div>
        </div>
    </AppLayout>
</template>
