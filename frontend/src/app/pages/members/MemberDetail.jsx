// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { useNavigate, useParams, Link } from "react-router";
import { PencilIcon, ArrowLeftIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card } from "components/ui";
import { membersAPI } from "services/api";

// ----------------------------------------------------------------------

export default function MemberDetail() {
 const { id } = useParams();
 const navigate = useNavigate();
 const [member, setMember] = useState(null);
 const [loading, setLoading] = useState(true);

 const fetchMember = useCallback(async () => {
  if (!id) return;
   try {
     setLoading(true);
     const response = await membersAPI.getById(id);
     const data = response.data?.data || response.data;
     setMember(data);
   } catch (error) {
     console.error("Error fetching member:", error);
     toast.error("Failed to load member details");
     navigate("/members");
   } finally {
     setLoading(false);
   }
 }, [id, navigate]);

 useEffect(() => {
   fetchMember();
 }, [fetchMember]);


 if (loading) {
   return (
     <Page title="Member Details">
       <div className="flex h-96 items-center justify-center">
         <p className="text-gray-500">Loading...</p>
       </div>
     </Page>
   );
 }

 if (!member) {
   return null;
 }

 return (
   <Page title="Member Details">
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div className="flex items-center gap-3">
           <Button
             variant="outlined"
             size="sm"
             onClick={() => navigate("/members")}
           >
             <ArrowLeftIcon className="size-4" />
           </Button>
           <div>
             <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
               Member Details
             </h2>
             <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
               {member.surname} {member.otherNames}
             </p>
           </div>
         </div>
         <Link to={`/members/${id}/edit`}>
           <Button color="primary" className="gap-2">
             <PencilIcon className="size-5" />
             Edit Member
           </Button>
         </Link>
       </div>

       {/* Member Information */}
       <div className="grid gap-5 md:grid-cols-2">
         {/* Personal Information */}
         <Card className="p-6">
           <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
             Personal Information
           </h3>
           <div className="space-y-3">
             <InfoRow label="Coop ID" value={member.coopId} />
             <InfoRow label="Surname" value={member.surname} />
             <InfoRow label="Other Names" value={member.otherNames} />
             <InfoRow label="Gender" value={member.gender} />
             <InfoRow label="Occupation" value={member.occupation} />
             <InfoRow label="Religion" value={member.religion} />
             <InfoRow label="Phone Number" value={member.phoneNumber} />
             <InfoRow label="Year Joined" value={member.yearJoined} />
           </div>
         </Card>

         {/* Bank & Next of Kin */}
         <div className="space-y-5 mx-4 my-4">
           <Card className="p-6">
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Bank Information
             </h3>
             <div className="space-y-3">
               <InfoRow label="Bank Name" value={member.bankName} />
               <InfoRow label="Account Number" value={member.accountNumber} />
             </div>
           </Card>

           <Card className="p-6">
             <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
               Next of Kin
             </h3>
             <div className="space-y-3">
               <InfoRow label="Name" value={member.nextOfKinName} />
               <InfoRow label="Phone Number" value={member.nextOfKinPhoneNumber} />
             </div>
           </Card>
         </div>
       </div>
     </div>
   </Page>
 );
}

function InfoRow({ label, value }) {
 return (
   <div className="flex justify-between border-b border-gray-100 pb-2 dark:border-dark-500">
     <span className="text-sm font-medium text-gray-600 dark:text-dark-300">
       {label}:
     </span>
     <span className="text-sm text-gray-900 dark:text-dark-100">
       {value || "N/A"}
     </span>
   </div>
 );
}