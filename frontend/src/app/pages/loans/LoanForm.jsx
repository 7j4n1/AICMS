// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { loansAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
 coopId: yup.string().required("Coop ID is required").min(1),
 loanAmount: yup.number().required("Loan amount is required").min(1, "Amount must be greater than 0"),
 loanDate: yup.string().required("Loan date is required"),
 guarantor1: yup.string().required("At least one guarantor is required"),
 guarantor2: yup.string().nullable(),
 guarantor3: yup.string().nullable(),
 guarantor4: yup.string().nullable(),
});

export default function LoanForm() {
 const { id } = useParams();
 const navigate = useNavigate();
 const [loading, setLoading] = useState(false);
 const isEdit = Boolean(id);

 const {
   register,
   handleSubmit,
   reset,
   formState: { errors },
 } = useForm({
   resolver: yupResolver(schema),
 });

 const fetchLoan = useCallback(async () => {
   if (!id) return;
   try {
     const response = await loansAPI.getById(id);
     const loan = response.data?.data || response.data;
     reset(loan);
   } catch (error) {
     console.error("Error fetching loan:", error);
     toast.error("Failed to load loan details");
   }
 }, [id, reset]);

 useEffect(() => {
   if (isEdit) {
     fetchLoan();
   }
 }, [isEdit, fetchLoan]);

 const onSubmit = async (data) => {
   try {
     setLoading(true);
     if (isEdit) {
       await loansAPI.update(id, data);
       toast.success("Loan updated successfully");
     } else {
       await loansAPI.create(data);
       toast.success("Loan created successfully");
     }
     navigate("/loans");
   } catch (error) {
     console.error("Error saving loan:", error);
     const errorMsg = error.response?.data?.message || error.message || "Failed to save loan";
     toast.error(errorMsg);
   } finally {
     setLoading(false);
   }
 };

 return (
   <Page title={isEdit ? "Edit Loan" : "Create Loan"}>
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div>
         <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
           {isEdit ? "Edit Loan" : "Create New Loan"}
         </h2>
         <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
           {isEdit ? "Update loan information" : "Create a new loan for a member"}
         </p>
       </div>

       {/* Form */}
       <Card className="p-6">
         <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
           {/* Loan Information */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Loan Information
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Coop ID"
                 placeholder="Enter member Coop ID"
                 {...register("coopId")}
                 error={errors?.coopId?.message}
                 disabled={isEdit}
               />
               <Input
                 label="Loan Amount"
                 type="number"
                 placeholder="Enter loan amount"
                 {...register("loanAmount")}
                 error={errors?.loanAmount?.message}
               />
               <Input
                 label="Loan Date"
                 type="date"
                 {...register("loanDate")}
                 error={errors?.loanDate?.message}
               />
             </div>
           </div>

           {/* Guarantors */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Guarantors
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Guarantor 1 Coop ID"
                 placeholder="Enter guarantor 1 Coop ID"
                 {...register("guarantor1")}
                 error={errors?.guarantor1?.message}
               />
               <Input
                 label="Guarantor 2 Coop ID"
                 placeholder="Enter guarantor 2 Coop ID"
                 {...register("guarantor2")}
                 error={errors?.guarantor2?.message}
               />
               <Input
                 label="Guarantor 3 Coop ID (Optional)"
                 placeholder="Enter guarantor 3 Coop ID"
                 {...register("guarantor3")}
                 error={errors?.guarantor3?.message}
               />
               <Input
                 label="Guarantor 4 Coop ID (Optional)"
                 placeholder="Enter guarantor 4 Coop ID"
                 {...register("guarantor4")}
                 error={errors?.guarantor4?.message}
               />
             </div>
           </div>

           {/* Actions */}
           <div className="flex gap-3">
             <Button
               type="submit"
               color="primary"
               disabled={loading}
             >
               {loading ? "Saving..." : isEdit ? "Update Loan" : "Create Loan"}
             </Button>
             <Button
               type="button"
               variant="outlined"
               onClick={() => navigate("/loans")}
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