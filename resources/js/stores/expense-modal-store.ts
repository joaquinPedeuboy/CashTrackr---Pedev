import { Budget } from "@/Pages/types/budget";
import { Category } from "@/Pages/types/category";
import { Expense } from "@/Pages/types/expense";
import { create } from "zustand";
import { devtools } from "zustand/middleware";

type ExpenseModalStore = {
    open: boolean
    budget: Budget | null
    expense: Expense | null
    categories: Category[]
    openCreateModal: () => void
    openEditModal: (expense: Expense) => void
    closeModal: () => void
    setBudget: (budget: Budget) => void
    setCategory: (categories: Category[]) => void
}
export const useExpenseModalStore = create<ExpenseModalStore>()(devtools((set) => ({
    open: false,
    budget:null,
    expense: null,
    categories: [],
    openCreateModal: () => {
        set({
            open: true
        })
    },
    openEditModal: (expense) => {
        set({
            open: true,
            expense
        })
    },
    closeModal: () => {
        set({
            open: false,
            expense:null
        })
    },
    setBudget: (budget) => {
        set({
            budget
        })
    },
    setCategory: (categories) => {
        set({
            categories
        })
    },
})))