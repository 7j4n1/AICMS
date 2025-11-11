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
import { membersAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
 coopId: yup.string().required("Coop ID is required"),
 surname: yup.string().required("Surname is required").max(255),
 otherNames: yup.string().nullable().max(255),
 occupation: yup.string().nullable().max(255),
 gender: yup.string().nullable().max(10),
 religion: yup.string().nullable().max(255),
 phoneNumber: yup.string().nullable().max(20),
 bankName: yup.string().nullable().max(255),
 accountNumber: yup.string().nullable().max(50),
 nextOfKinName: yup.string().nullable().max(255),
 nextOfKinPhoneNumber: yup.string().nullable().max(20),
 yearJoined: yup.number().nullable().min(1900).max(new Date().getFullYear()),
});

export default function MemberForm() {
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

 

 const fetchMember = useCallback(async () => {
   if (!id) return;

   try {
     const response = await membersAPI.getById(id);
     const member = response.data?.data || response.data;
     reset(member);
   } catch (error) {
     console.error("Error fetching member:", error);
     toast.error("Failed to load member details");
   }
 }, [id, reset]);

 useEffect(() => {
   if (isEdit) {
     fetchMember();
   }
 }, [isEdit, fetchMember]);

 const onSubmit = async (data) => {
   try {
     setLoading(true);
     if (isEdit) {
       await membersAPI.update(id, data);
       toast.success("Member updated successfully");
     } else {
       await membersAPI.create(data);
       toast.success("Member created successfully");
     }
     navigate("/members/all");
   } catch (error) {
     console.error("Error saving member:", error);
     const errorMsg = error.response?.data?.message || error.message || "Failed to save member";
     toast.error(errorMsg);
   } finally {
     setLoading(false);
   }
 };

 return (
   <Page title={isEdit ? "Edit Member" : "Create Member"}>
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div>
         <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
           {isEdit ? "Edit Member" : "Create New Member"}
         </h2>
         <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
           {isEdit ? "Update member information" : "Add a new member to the cooperative"}
         </p>
       </div>

       {/* Form */}
       <Card className="p-6">
         <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
           {/* Personal Information */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Personal Information
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Coop ID"
                 placeholder="Enter Coop ID"
                 {...register("coopId")}
                 error={errors?.coopId?.message}
                 disabled={isEdit}
               />
               <Input
                 label="Surname"
                 placeholder="Enter surname"
                 {...register("surname")}
                 error={errors?.surname?.message}
               />
               <Input
                 label="Other Names"
                 placeholder="Enter other names"
                 {...register("otherNames")}
                 error={errors?.otherNames?.message}
               />
               <Input
                 label="Occupation"
                 placeholder="Enter occupation"
                 {...register("occupation")}
                 error={errors?.occupation?.message}
               />
               <Input
                 label="Gender"
                 placeholder="Enter gender"
                 {...register("gender")}
                 error={errors?.gender?.message}
               />
               <Input
                 label="Religion"
                 placeholder="Enter religion"
                 {...register("religion")}
                 error={errors?.religion?.message}
               />
               <Input
                 label="Phone Number"
                 placeholder="Enter phone number"
                 {...register("phoneNumber")}
                 error={errors?.phoneNumber?.message}
               />
               <Input
                 label="Year Joined"
                 type="number"
                 placeholder="Enter year joined"
                 {...register("yearJoined")}
                 error={errors?.yearJoined?.message}
               />
             </div>
           </div>

           {/* Bank Information */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Bank Information
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Bank Name"
                 placeholder="Enter bank name"
                 {...register("bankName")}
                 error={errors?.bankName?.message}
               />
               <Input
                 label="Account Number"
                 placeholder="Enter account number"
                 {...register("accountNumber")}
                 error={errors?.accountNumber?.message}
               />
             </div>
           </div>

           {/* Next of Kin */}
           <div>
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Next of Kin Information
             </h3>
             <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
               <Input
                 label="Next of Kin Name"
                 placeholder="Enter next of kin name"
                 {...register("nextOfKinName")}
                 error={errors?.nextOfKinName?.message}
               />
               <Input
                 label="Next of Kin Phone"
                 placeholder="Enter next of kin phone"
                 {...register("nextOfKinPhoneNumber")}
                 error={errors?.nextOfKinPhoneNumber?.message}
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
               {loading ? "Saving..." : isEdit ? "Update Member" : "Create Member"}
             </Button>
             <Button
               type="button"
               variant="outlined"
               onClick={() => navigate("/members")}
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