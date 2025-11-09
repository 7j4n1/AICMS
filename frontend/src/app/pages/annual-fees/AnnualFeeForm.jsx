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
import { annualFeesAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
 coopId: yup.string().required("Coop ID is required"),
 annual_year: yup.number().required("Year is required").min(1900).max(new Date().getFullYear() + 1),
 annual_savings: yup.number().required("Annual savings is required").min(0),
 annual_fee: yup.number().required("Annual fee is required").min(0),
});

export default function AnnualFeeForm() {
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

 const fetchAnnualFee = useCallback(async () => {
   if (!id) return;
   try {
     const response = await annualFeesAPI.getById(id);
     const fee = response.data?.data || response.data;
     reset(fee);
   } catch (error) {
     console.error("Error fetching annual fee:", error);
     toast.error("Failed to load annual fee details");
   }
 }, [id, reset]);

 useEffect(() => {
   if (isEdit) {
     fetchAnnualFee();
   }
 }, [isEdit, fetchAnnualFee]);

 const onSubmit = async (data) => {
   try {
     setLoading(true);
     if (isEdit) {
       await annualFeesAPI.update(id, data);
       toast.success("Annual fee updated successfully");
     } else {
       await annualFeesAPI.create(data);
       toast.success("Annual fee created successfully");
     }
     navigate("/annual-fees");
   } catch (error) {
     console.error("Error saving annual fee:", error);
     const errorMsg = error.response?.data?.message || error.message || "Failed to save annual fee";
     toast.error(errorMsg);
   } finally {
     setLoading(false);
   }
 };

 return (
   <Page title={isEdit ? "Edit Annual Fee" : "Create Annual Fee"}>
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div>
         <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
           {isEdit ? "Edit Annual Fee" : "Create Annual Fee"}
         </h2>
         <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
           {isEdit ? "Update annual fee information" : "Record annual fee for a member"}
         </p>
       </div>

       {/* Form */}
       <Card className="p-6">
         <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
           <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
             <Input
               label="Coop ID"
               placeholder="Enter member Coop ID"
               {...register("coopId")}
               error={errors?.coopId?.message}
               disabled={isEdit}
             />
             <Input
               label="Year"
               type="number"
               placeholder="Enter year"
               {...register("annual_year")}
               error={errors?.annual_year?.message}
             />
             <Input
               label="Annual Savings"
               type="number"
               placeholder="Enter annual savings amount"
               {...register("annual_savings")}
               error={errors?.annual_savings?.message}
             />
             <Input
               label="Annual Fee"
               type="number"
               placeholder="Enter annual fee amount"
               {...register("annual_fee")}
               error={errors?.annual_fee?.message}
             />
           </div>

           {/* Actions */}
           <div className="flex gap-3">
             <Button
               type="submit"
               color="primary"
               disabled={loading}
             >
               {loading ? "Saving..." : isEdit ? "Update Annual Fee" : "Create Annual Fee"}
             </Button>
             <Button
               type="button"
               variant="outlined"
               onClick={() => navigate("/annual-fees")}
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