// Import Dependencies
import { useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { paymentsAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
 coopId: yup.string().required("Coop ID is required"),
 paymentDate: yup.string().required("Payment date is required"),
 loanAmount: yup.number().min(0, "Amount cannot be negative").nullable(),
 savingAmount: yup.number().min(0, "Amount cannot be negative").nullable(),
 shareAmount: yup.number().min(0, "Amount cannot be negative").nullable(),
 others: yup.number().min(0, "Amount cannot be negative").nullable(),
 adminCharge: yup.number().min(0, "Amount cannot be negative").nullable(),
 splitOption: yup.string().nullable(),
 otherSavingsType: yup.string().nullable(),
});

export default function PaymentForm() {
 const navigate = useNavigate();
 const [loading, setLoading] = useState(false);

 const {
   register,
   handleSubmit,
   formState: { errors },
   watch,
 } = useForm({
   resolver: yupResolver(schema),
   defaultValues: {
     loanAmount: 0,
     savingAmount: 0,
     shareAmount: 0,
     others: 0,
     adminCharge: 0,
   },
 });

 const loanAmount = watch("loanAmount") || 0;
 const savingAmount = watch("savingAmount") || 0;
 const shareAmount = watch("shareAmount") || 0;
 const others = watch("others") || 0;
 const adminCharge = watch("adminCharge") || 0;

 const totalAmount = Number(loanAmount) + Number(savingAmount) + Number(shareAmount) + Number(others) + Number(adminCharge);

 const onSubmit = async (data) => {
   try {
     setLoading(true);
     await paymentsAPI.create(data);
     toast.success("Payment recorded successfully");
     navigate("/payments");
   } catch (error) {
     console.error("Error creating payment:", error);
     const errorMsg = error.response?.data?.message || error.message || "Failed to record payment";
     toast.error(errorMsg);
   } finally {
     setLoading(false);
   }
 };

 const formatCurrency = (amount) => {
   return new Intl.NumberFormat("en-NG", {
     style: "currency",
     currency: "NGN",
   }).format(amount);
 };

 return (
   <Page title="Record Payment">
     <div className="space-y-5">
       {/* Header */}
       <div>
         <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
           Record Payment
         </h2>
         <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
           Record a new payment from a member
         </p>
       </div>

       {/* Form */}
       <Card className="p-6">
         <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
           {/* Basic Information */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Payment Information
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Coop ID"
                 placeholder="Enter member Coop ID"
                 {...register("coopId")}
                 error={errors?.coopId?.message}
               />
               <Input
                 label="Payment Date"
                 type="date"
                 {...register("paymentDate")}
                 error={errors?.paymentDate?.message}
               />
             </div>
           </div>

           {/* Payment Breakdown */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Payment Breakdown
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Loan Repayment"
                 type="number"
                 placeholder="0"
                 {...register("loanAmount")}
                 error={errors?.loanAmount?.message}
               />
               <Input
                 label="Savings Amount"
                 type="number"
                 placeholder="0"
                 {...register("savingAmount")}
                 error={errors?.savingAmount?.message}
               />
               <Input
                 label="Share Amount"
                 type="number"
                 placeholder="0"
                 {...register("shareAmount")}
                 error={errors?.shareAmount?.message}
               />
               <Input
                 label="Others"
                 type="number"
                 placeholder="0"
                 {...register("others")}
                 error={errors?.others?.message}
               />
               <Input
                 label="Admin Charge"
                 type="number"
                 placeholder="0"
                 {...register("adminCharge")}
                 error={errors?.adminCharge?.message}
               />
               <Input
                 label="Split Option"
                 placeholder="e.g., monthly, weekly"
                 {...register("splitOption")}
                 error={errors?.splitOption?.message}
               />
               <Input
                 label="Other Savings Type"
                 placeholder="e.g., special, emergency"
                 {...register("otherSavingsType")}
                 error={errors?.otherSavingsType?.message}
               />
             </div>
           </div>

           {/* Total */}
           <div className="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-dark-500 dark:bg-dark-600">
             <div className="flex items-center justify-between">
               <span className="text-lg font-semibold text-gray-700 dark:text-dark-100">
                 Total Amount:
               </span>
               <span className="text-2xl font-bold text-primary-600 dark:text-primary-400">
                 {formatCurrency(totalAmount)}
               </span>
             </div>
           </div>

           {/* Actions */}
           <div className="flex gap-3">
             <Button
               type="submit"
               color="primary"
               disabled={loading}
             >
               {loading ? "Recording..." : "Record Payment"}
             </Button>
             <Button
               type="button"
               variant="outlined"
               onClick={() => navigate("/payments")}
             >
               Cancel
             </Button>
           </div>
         </form>
       </Card>
     </div>
   </Page>
 );
}