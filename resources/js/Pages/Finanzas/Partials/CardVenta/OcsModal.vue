<script setup>
import { computed, ref, watch } from 'vue';
import { formatoMoney } from "../../../../utils/conversiones";
import ButtonAdd from '@/Components/ButtonAdd.vue';
import DialogModal from '@/Components/DialogModal.vue';
import TableComponent from '@/Components/Table.vue';
import FormOcModal from './FormOcModal.vue';
import ItemOc from './ItemOc.vue';

const emit = defineEmits(["close", "showAddVenta"])
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    venta: {
        type: Object,
        required: true
    },
})

const ocs = ref([])
const showingFormOc = ref(false)
const oc = ref({ id: -1 });
const typeForm = ref("create");

const title = computed(() => {
    switch (props.venta) {
        case "1":
            return "Por Pagar"
        case "2":
            return "Cerrado"
        default:
            return "Por Pagar"
    }
})


const getOcs = async () => {
    const resp = await axios.get(route('ocs.index') + `?venta_id=${props.venta.id}`);
    ocs.value = resp.data;
}

// Methos Modal
const showFormOc = (ocSelected) => {

    if (ocSelected != undefined) {
        oc.value = ocSelected;
        typeForm.value = "update";
    } else {
        oc.value = { id: -1 };
        typeForm.value = "create";
    }
    showingFormOc.value = true;
}

const addOc = (newOc) => {
    ocs.value.unshift(newOc);
}


const editOc = (newOc) => {
    const findIndex = ocs.value.findIndex(ocFind => {
        return newOc.id == ocFind.id;
    })
    if (findIndex !== -1) {

        ocs.value[findIndex] = newOc;
    }
}


const deleteOc = (ocSelected) => {
    const ocIndex = ocs.value.findIndex(ocFind => {
        return ocFind.id === ocSelected.id;
    })
    axios.delete(route('ocs.destroy', ocSelected.id))
        .then(() => {
            ocs.value.splice(ocIndex, 1);
            Inertia.visit(route('ventas.index'), {
                preserveState: true,
                preserveScroll: true,
                only: ['totalOcs'],
            })
        }).catch(error => {

            if (error.response.data.hasOwnProperty('message')) {
                alert(error.response.data.message)
            } else {
                alert("ERROR DELETE OC");
            }
        });
}

// End Methos Modal

const close = () => {
    ocs.value = [];
    emit('close');
};

watch(props, () => {
    if (props.show == true) {
        getOcs();
    }
})

</script>
<template>
    <DialogModal :show="show" @close="close()">
        <template #title>
            <div class="flex flex-row">
                <div class="px-4 py-1 border-r-4 border-gray-600 basis-1/3">
                    <span class="block font-bold text-center text-white">
                        {{ title }}
                    </span>
                </div>
                <div class="flex-1 px-2 py-1">
                    <div class="flex justify-center">
                        <span class="block font-bold text-center text-gray-300">
                            Fecha Incial {{ venta.fechaInicial }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-row">
                <div class="px-4 py-1 border-t-4 border-r-4 border-gray-600 basis-1/3">
                    <span class="block font-bold text-gray-300">
                        Subtotal:
                        ${{ formatoMoney(venta.monto) }}
                    </span>
                </div>
                <div class="px-4 py-1 border-t-4 border-r-4 border-gray-600 basis-1/3">
                    <span lass="block font-bold text-center text-gray-300">
                        <p class="text-gray-300" v-if="venta.iva == true">
                            IVA:
                            ${{ formatoMoney(Math.round(venta.monto * 0.16)) }}
                        </p>
                        <p class="text-gray-300" v-if="venta.iva == false">
                            IVA:
                            ${{ 0 }}
                        </p>
                    </span>
                </div>
                <div class="px-4 py-1 border-t-4 border-r-4 border-gray-600 basis-1/3">
                    <span class="block font-bold text-center text-gray-300">
                        Total:
                        ${{ formatoMoney(venta.monto + (venta.monto * 0.16)) }}
                    </span>
                </div>
            </div>
            <div class="px-4 py-1 border-t-4 border-r-4 border-gray-600 basis-1/3">
                <span class="block font-bold text-gray-300">
                    COMENTARIO:
                    {{ venta.comentario }}
                </span>
            </div>
        </template>
        <template #content>
            <TableComponent>
                <template #thead>
                    <tr>
                        <th>
                            <h3 class="mb-1">OC</h3>
                            <ButtonAdd v-if="$page.props.can['ocs.create']" class="h-5" @click="showFormOc()" />
                        </th>
                        <th>CANTIDAD</th>
                        <th>FECHA</th>
                        <th v-if="$page.props.can['ocs.edit']"></th>
                        <th v-if="$page.props.can['ocs.delete']"></th>
                    </tr>
                </template>
                <template #tbody>
                    <ItemOc v-for="oc in ocs" :key="oc.id" :oc="oc" @edit="showFormOc($event)"
                        @delete="deleteOc($event)" />
                </template>
            </TableComponent>
            <!--Modals-->
            <FormOcModal :show="showingFormOc" :type-form="typeForm" :venta="props.venta" :oc="oc"
                @add-oc="addOc($event)" @edit-oc="editOc($event)" @close="showingFormOc = false" />
            <!-- Ends Mondals -->
        </template>
    </DialogModal>
</template>
